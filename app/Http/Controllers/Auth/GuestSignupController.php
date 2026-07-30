<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Http\Requests\GuestSignupPhoneRequest;
use App\Libraries\AppLibrary;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\PermissionService;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Enums\Role as EnumRole;
use App\Services\OtpManagerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\VerifyPhoneRequest;

class GuestSignupController extends Controller
{
    private OtpManagerService $otpManagerService;

    public function __construct(OtpManagerService $otpManagerService)
    {
        $this->otpManagerService = $otpManagerService;
    }


    public function phone(GuestSignupPhoneRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {

            if (Settings::group('site')->get('site_phone_verification') == Activity::ENABLE) {
                $this->otpManagerService->phoneOTP($request);
            }
            return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(VerifyPhoneRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO') || Settings::group('site')->get('site_phone_verification') == Activity::DISABLE) {
                return $this->register(['code' => $request->code, 'phone' => $request->phone]);
            }

            if ($this->otpManagerService->phoneVerify($request)) {
                return $this->register(['code' => $request->code, 'phone' => $request->phone, 'token' => $request->token]);
            }
            return response(['status' => false, 'message' => trans('all.message.something_wrong')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * @throws Exception
     */
    private function register($array): JsonResponse
    {
        $user = User::where(['country_code' => $array['code'], 'phone' => $array['phone']])->first();
        if (!$user) {
            $name = "Guest User";
            $user = User::create([
                'name'                 => $name,
                'username'             => Str::slug($name) . AppLibrary::timeWithRand(),
                'phone'                => $array['phone'],
                'country_code'         => $array['code'],
                'restaurant_id'        => 0,
                'email_verified_at'    => Carbon::now()->getTimestamp(),
                'is_guest'             => Ask::YES,
                'password'             => Hash::make(rand(111111, 999999)),
                'terms_and_conditions' => Ask::NO
            ]);
            $user->assignRole(EnumRole::CUSTOMER);
        }

        if (!Auth::guard('web')->loginUsingId($user->id)) {
            return new JsonResponse([
                'errors' => ['validation' => trans('all.message.credentials_invalid')]
            ], 400);
        }

        return app(LoginController::class, [
            MenuService::class,
            PermissionService::class,
            DefaultAccessService::class
        ])->permissionManager($user);
    }
}
