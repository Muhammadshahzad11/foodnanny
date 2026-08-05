<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Http\Requests\SignupPhoneRequest;
use App\Http\Requests\SignupRequest;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\OtpManagerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\VerifyPhoneRequest;
use App\Enums\Role as EnumRole;

class SignupController extends Controller
{

    private OtpManagerService $otpManagerService;

    public function __construct(OtpManagerService $otpManagerService)
    {
        $this->otpManagerService = $otpManagerService;
    }

    public function phone(SignupPhoneRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $user = User::where(['country_code' => $request->post('code'), 'phone' => $request->post('phone')])->first();
            if ($user) {
                return response(['status' => false, 'message' => trans("all.message.phone_exist")], 422);
            } else {
                $payload = ['status' => true, 'message' => trans("all.message.check_your_phone_for_code")];
                if (Settings::group('site')->get('site_phone_verification') == Activity::ENABLE) {
                    $otp = $this->otpManagerService->phoneOTP($request);
                    if (filter_var(env('SHOW_OTP', true), FILTER_VALIDATE_BOOLEAN)) {
                        $payload['otp'] = $otp;
                    }
                }
                return response($payload, 200);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(VerifyPhoneRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->otpManagerService->phoneVerify($request, false);
            return response(['status' => true, 'message' => trans("all.message.otp_verify_success")], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function register(SignupRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            DB::transaction(function () use ($request) {
                $name = $request->post('first_name') . ' ' . $request->post('last_name');
                $user = User::create([
                    'name'                 => $name,
                    'username'             => Str::slug($name) . AppLibrary::timeWithRand(),
                    'email'                => $request->post('email'),
                    'phone'                => $request->post('phone'),
                    'country_code'         => $request->post('country_code'),
                    'restaurant_id'        => 0,
                    'email_verified_at'    => Carbon::now()->getTimestamp(),
                    'is_guest'             => Ask::NO,
                    'password'             => Hash::make($request->post('password')),
                    'terms_and_conditions' => $request->post('page_id') > 0 ? $request->post('terms_and_conditions') : Ask::NO,
                ]);
                $user->assignRole(EnumRole::CUSTOMER);
            });
            return response(['status' => true, 'message' => trans('all.message.register_successfully')], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
