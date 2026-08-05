<?php

namespace App\Http\Controllers\Auth;


use App\Enums\Activity;
use App\Enums\PermissionType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\GuestSignupPhoneRequest;
use App\Http\Requests\VerifyPhoneRequest;
use App\Http\Resources\LoginUserResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Libraries\AppLibrary;
use App\Models\User;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\OtpManagerService;
use App\Services\PermissionService;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public string $token;
    public DefaultAccessService $defaultAccessService;
    public PermissionService $permissionService;
    public MenuService $menuService;
    public OtpManagerService $otpManagerService;

    public function __construct(
        MenuService          $menuService,
        PermissionService    $permissionService,
        DefaultAccessService $defaultAccessService,
        OtpManagerService    $otpManagerService
    )
    {
        $this->menuService          = $menuService;
        $this->permissionService    = $permissionService;
        $this->defaultAccessService = $defaultAccessService;
        $this->otpManagerService    = $otpManagerService;
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
                'status'       => Status::ACTIVE,
            ])->first();

            if (!$user) {
                return new JsonResponse([
                    'status'  => false,
                    'message' => trans('all.message.phone_not_registered'),
                    'errors'  => ['phone' => [trans('all.message.phone_not_registered')]],
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
            if (filter_var(env('SHOW_OTP', true), FILTER_VALIDATE_BOOLEAN)) {
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
                'status'       => Status::ACTIVE,
            ])->first();

            if (!$user) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.phone_not_registered')],
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

        $restaurantId = $user->restaurant_id;
        $permission   = $permission->filter(function ($p) use ($restaurantId) {
            if ($restaurantId == 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::ADMIN)) {
                return $p;
            } elseif ($restaurantId > 0 && ($p->type == PermissionType::BOTH || $p->type == PermissionType::RESTAURANT_OWNER)) {
                return $p;
            }
        });

        $adminPermission      = PermissionResource::collection($adminPermission);
        $restaurantPermission = PermissionResource::collection($restaurantPermission);
        $menuServiceMenu      = $this->menuService->menu($user, $user->roles[0]);

        return new JsonResponse([
            'message'                       => trans('all.message.login_success'),
            'token'                         => $this->token,
            'user'                          => new LoginUserResource($user),
            'admin_menu'                    => MenuResource::collection(collect($menuServiceMenu['adminPermission'])),
            'restaurant_menu'               => MenuResource::collection(collect($menuServiceMenu['restaurantPermission'])),
            'admin_permission'              => $adminPermission,
            'restaurant_permission'         => $restaurantPermission,
            'permission'                    => PermissionResource::collection($permission),
            'admin_default_permission'      => count($menuServiceMenu['adminPermission']) > 0 ? AppLibrary::defaultPermission($adminPermission) : (object)[],
            'restaurant_default_permission' => count($menuServiceMenu['restaurantPermission']) > 0 ? AppLibrary::defaultPermission($restaurantPermission) : (object)[],
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
}
