<?php

namespace App\Http\Controllers\Auth;


use App\Enums\Activity;
use App\Enums\Apply;
use App\Enums\PermissionType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\GuestSignupPhoneRequest;
use App\Http\Requests\VerifyPhoneRequest;
use App\Http\Resources\LoginUserResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Libraries\AppLibrary;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\OtpManagerService;
use App\Services\PermissionService;
use App\Services\RestaurantModuleService;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public string $token;
    public DefaultAccessService $defaultAccessService;
    public PermissionService $permissionService;
    public MenuService $menuService;
    public OtpManagerService $otpManagerService;
    public RestaurantModuleService $restaurantModuleService;

    public function __construct(
        MenuService              $menuService,
        PermissionService        $permissionService,
        DefaultAccessService     $defaultAccessService,
        OtpManagerService        $otpManagerService,
        RestaurantModuleService  $restaurantModuleService
    )
    {
        $this->menuService              = $menuService;
        $this->permissionService        = $permissionService;
        $this->defaultAccessService     = $defaultAccessService;
        $this->otpManagerService        = $otpManagerService;
        $this->restaurantModuleService  = $restaurantModuleService;
    }

    /**
     * @throws \Exception
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'errors' => $validator->errors()
            ], 422);
        }

        $request->merge(['status' => Status::ACTIVE]);

        if (!Auth::guard('web')->attempt($request->only('email', 'password', 'status'))) {
            $user = User::where('email', $request->input('email'))->first();
            if ($user && Hash::check($request->input('password'), $user->password)) {
                if ($this->isPendingRestaurantOwner($user)) {
                    return new JsonResponse([
                        'errors' => ['validation' => trans('all.message.restaurant_pending_approval')]
                    ], 400);
                }
            }

            return new JsonResponse([
                'errors' => ['validation' => trans('all.message.credentials_invalid')]
            ], 400);
        }

        $user = User::where('email', $request['email'])->first();
        return $this->permissionManager($user);
    }

    /**
     * Send OTP for phone login (existing users only — no password).
     */
    public function sendPhoneLoginOtp(GuestSignupPhoneRequest $request): JsonResponse
    {
        try {
            $user = User::where([
                'country_code' => $request->post('code'),
                'phone'        => $request->post('phone'),
            ])->first();

            if (!$user) {
                return new JsonResponse([
                    'status'  => false,
                    'message' => trans('all.message.phone_not_registered'),
                    'errors'  => ['phone' => [trans('all.message.phone_not_registered')]],
                ], 422);
            }

            if ($this->isPendingRestaurantOwner($user)) {
                return new JsonResponse([
                    'status'  => false,
                    'message' => trans('all.message.restaurant_pending_approval'),
                    'errors'  => ['phone' => [trans('all.message.restaurant_pending_approval')]],
                ], 422);
            }

            if ((int)$user->status !== Status::ACTIVE) {
                return new JsonResponse([
                    'status'  => false,
                    'message' => trans('all.message.credentials_invalid'),
                    'errors'  => ['phone' => [trans('all.message.credentials_invalid')]],
                ], 422);
            }

            // Demo / phone verification off → frontend can verify with a dummy token
            if (env('DEMO') || Settings::group('site')->get('site_phone_verification') == Activity::DISABLE) {
                return new JsonResponse([
                    'status'   => true,
                    'skip_otp' => true,
                    'message'  => trans('all.message.login_success'),
                ], 200);
            }

            $payload = [
                'status'   => true,
                'skip_otp' => false,
                'message'  => trans('all.message.check_your_phone_for_code'),
            ];

            $otp = $this->otpManagerService->phoneOTP($request);
            if (OtpManagerService::shouldExposeOtp()) {
                $payload['otp'] = $otp;
            }

            return new JsonResponse($payload, 200);
        } catch (Exception $exception) {
            return new JsonResponse([
                'status'  => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    /**
     * Verify OTP and log in with phone (no password).
     *
     * @throws \Exception
     */
    public function phoneLogin(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $skipOtp = env('DEMO')
                || Settings::group('site')->get('site_phone_verification') == Activity::DISABLE;

            if (!$skipOtp) {
                $this->otpManagerService->phoneVerify($request);
            }

            $user = User::where([
                'country_code' => $request->post('code'),
                'phone'        => $request->post('phone'),
            ])->first();

            if (!$user) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.phone_not_registered')],
                ], 400);
            }

            if ($this->isPendingRestaurantOwner($user)) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.restaurant_pending_approval')],
                ], 400);
            }

            if ((int)$user->status !== Status::ACTIVE) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.credentials_invalid')],
                ], 400);
            }

            if (!Auth::guard('web')->loginUsingId($user->id)) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.credentials_invalid')],
                ], 400);
            }

            return $this->permissionManager($user);
        } catch (Exception $exception) {
            return new JsonResponse([
                'status'  => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    /**
     * Self-registered restaurant owners stay inactive until an admin approves the restaurant.
     */
    private function isPendingRestaurantOwner(?User $user): bool
    {
        if (!$user || (int)$user->status !== Status::INACTIVE || (int)$user->restaurant_id <= 0) {
            return false;
        }

        $restaurant = Restaurant::find($user->restaurant_id);

        return $restaurant
            && (int)$restaurant->status === Status::INACTIVE
            && (int)$restaurant->apply === Apply::RESTAURANT_OWNER;
    }


    /**
     * @throws \Exception
     */
    public function permissionManager($user): JsonResponse
    {
        $this->token = $user->createToken('auth_token')->plainTextToken;
        $this->defaultAccessService->storeOrUpdate(['restaurant_id' => $user->restaurant_id]);
        if (!isset($user->roles[0])) {
            return new JsonResponse([
                'errors' => ['validation' => trans('all.message.role_exist')]
            ], 400);
        }

        $payload = $this->buildPermissionPayload($user);

        return new JsonResponse([
            'message'                       => trans('all.message.login_success'),
            'token'                         => $this->token,
            'user'                          => new LoginUserResource($user),
            'admin_menu'                    => $payload['admin_menu'],
            'restaurant_menu'               => $payload['restaurant_menu'],
            'admin_permission'              => $payload['admin_permission'],
            'restaurant_permission'         => $payload['restaurant_permission'],
            'permission'                    => $payload['permission'],
            'admin_default_permission'      => $payload['admin_default_permission'],
            'restaurant_default_permission' => $payload['restaurant_default_permission'],
        ], 201);
    }

    public function isAuth(): JsonResponse
    {
        if (Auth::check()) {
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return new JsonResponse([
            'message' => trans('all.message.logout_success')
        ], 200);
    }

    /**
     * Refresh sidebar menus + route permissions for the authenticated user (no new token).
     */
    public function menus(): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !isset($user->roles[0])) {
            return new JsonResponse([
                'errors' => ['validation' => trans('all.message.role_exist')]
            ], 400);
        }

        return new JsonResponse($this->buildPermissionPayload($user), 200);
    }

    private function buildPermissionPayload($user): array
    {
        $permission      = $this->permissionService->allPermission($user, $user->roles[0]);
        $adminPermission = $permission->filter(function ($p) {
            if ($p->type == PermissionType::BOTH || $p->type == PermissionType::ADMIN) {
                return $p;
            }
        });

        $restaurantPermission = $permission->filter(function ($p) {
            if ($p->type == PermissionType::BOTH || $p->type == PermissionType::RESTAURANT_OWNER) {
                return $p;
            }
        });

        $restaurantId = $this->restaurantModuleService->currentRestaurantId();
        $restaurant   = $this->restaurantModuleService->currentRestaurant();

        $activePermission = $permission->filter(function ($p) use ($restaurantId) {
            if ($restaurantId == 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::ADMIN)) {
                return $p;
            }
            if ($restaurantId > 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::RESTAURANT_OWNER)) {
                return $p;
            }
        });

        if ($restaurantId > 0) {
            $activePermission     = $this->restaurantModuleService->denyDisabledModules($activePermission, $restaurant);
            $restaurantPermission = $this->restaurantModuleService->denyDisabledModules($restaurantPermission, $restaurant);
        }

        $adminPermission      = PermissionResource::collection($adminPermission);
        $restaurantPermission = PermissionResource::collection($restaurantPermission);
        $menuServiceMenu      = $this->menuService->menu($user, $user->roles[0], $restaurant);

        return [
            'admin_menu'                    => MenuResource::collection(collect($menuServiceMenu['adminPermission'])),
            'restaurant_menu'               => MenuResource::collection(collect($menuServiceMenu['restaurantPermission'])),
            'admin_permission'              => $adminPermission,
            'restaurant_permission'         => $restaurantPermission,
            'permission'                    => PermissionResource::collection($activePermission),
            'admin_default_permission'      => count($menuServiceMenu['adminPermission']) > 0 ? AppLibrary::defaultPermission($adminPermission) : (object) [],
            'restaurant_default_permission' => count($menuServiceMenu['restaurantPermission']) > 0 ? AppLibrary::defaultPermission($restaurantPermission) : (object) [],
        ];
    }
}
