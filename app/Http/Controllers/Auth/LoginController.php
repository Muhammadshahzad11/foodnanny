<?php

namespace App\Http\Controllers\Auth;


use App\Enums\Ask;
use App\Enums\PermissionType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoginUserResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Libraries\AppLibrary;
use App\Models\User;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\PermissionService;
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

    public function __construct(
        MenuService          $menuService,
        PermissionService    $permissionService,
        DefaultAccessService $defaultAccessService
    )
    {
        $this->menuService          = $menuService;
        $this->permissionService    = $permissionService;
        $this->defaultAccessService = $defaultAccessService;
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
     * @throws \Exception
     */
    public function phoneLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country_code' => ['required', 'string', 'max:10'],
            'phone'        => ['required', 'string', 'max:200'],
            'password'     => ['required', 'string', 'min:6']
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'errors' => $validator->errors()
            ], 422);
        }

        $request->merge(['status' => Status::ACTIVE]);

        if (!Auth::guard('web')->attempt($request->only('country_code', 'phone', 'password', 'status'))) {
            return new JsonResponse([
                'errors' => ['validation' => trans('all.message.credentials_invalid')]
            ], 400);
        }

        $user = User::where(['country_code' => $request['country_code'], 'phone' => $request['phone']])->first();
        return $this->permissionManager($user);
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
