<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DeliveryBoySignupRequest;
use Exception;
use App\Enums\Ask;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\Activity;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use App\Enums\Role as EnumRole;
use App\Services\OtpManagerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\SignupPhoneRequest;
use App\Http\Requests\VerifyPhoneRequest;

class DeliveryBoySignupController extends Controller
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
                if (Settings::group('site')->get('site_phone_verification') == Activity::ENABLE) {
                    $this->otpManagerService->phoneOTP($request);
                }
                return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")], 200);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(VerifyPhoneRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->otpManagerService->phoneVerify($request, false);
            return response(['status' => true, 'message' => trans("all.message.otp_verify_success")], 201);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function register(DeliveryBoySignupRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = User::where(['country_code' => $request->post('country_code'), 'phone' => $request->post('phone'), 'is_guest' => Ask::YES])->first();
        if ($user) {
            $user->name                 = $request->post('name');
            $user->username             = Str::slug($request->post('name')) . AppLibrary::timeWithRand();
            $user->email                = $request->post('email');
            $user->password             = Hash::make($request->post('password'));
            $user->is_guest             = Ask::NO;
            $user->terms_and_conditions = $request->post('page_id') > 0 ? $request->post('terms_and_conditions') : Ask::NO;
            $user->save();
        } else {
            $user = User::create([
                'name'                 => $request->post('name'),
                'username'             => Str::slug($request->post('name')) . AppLibrary::timeWithRand(),
                'email'                => $request->post('email'),
                'phone'                => $request->post('phone'),
                'country_code'         => $request->post('country_code'),
                'restaurant_id'        => 0,
                'email_verified_at'    => Carbon::now()->getTimestamp(),
                'is_guest'             => Ask::NO,
                'password'             => Hash::make($request->post('password')),
                'terms_and_conditions' => $request->post('page_id') > 0 ? $request->post('terms_and_conditions') : Ask::NO,
            ]);
            $user->assignRole(EnumRole::DELIVERY_BOY);
        }
        return response(['status' => true, 'message' => trans('all.message.register_successfully')], 201);
    }
}
