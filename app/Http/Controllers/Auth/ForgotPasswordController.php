<?php

namespace App\Http\Controllers\Auth;


use App\Enums\Activity;
use App\Events\PasswordForgotten;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Dipokhalder\Settings\Facades\Settings;

class ForgotPasswordController extends Controller
{

    public int $pin;
    public string $token;

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:200'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->post('email'))->first();
        if (!blank($user)) {
            $verify = DB::table('password_reset_tokens')->where([
                ['email', $request->post('email')]
            ]);

            if ($verify->exists()) {
                $verify->delete();
            }

            $this->pin = rand(
                pow(10, (int)Settings::group('otp')->get('otp_digit_limit') - 1),
                pow(10, (int)Settings::group('otp')->get('otp_digit_limit')) - 1
            );

            $passwordReset = DB::table('password_reset_tokens')->insert([
                'email'      => $request->post('email'),
                'token'      => $this->pin,
                'created_at' => Carbon::now()
            ]);

            if (!blank($passwordReset)) {
                PasswordForgotten::dispatch(['name' => $user->name, 'email' => $request->post('email'), 'pin' => $this->pin]);
                return new JsonResponse([
                    'message' => trans('all.message.check_your_email_for_code')
                ], 200);
            } else {
                return new JsonResponse([
                    'errors' => ['email' => [trans('all.message.token_created_fail')]]
                ], 400);
            }
        } else {
            return new JsonResponse([
                'errors' => ['email' => [trans('all.message.email_does_not_exist')]]
            ], 400);
        }
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'code'  => ['required'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->errors()], 422);
        }

        $check = DB::table('password_reset_tokens')->where([
            ['email', $request->post('email')],
            ['token', $request->post('code')],
        ]);

        if ($check->exists()) {
            $difference = (int)Carbon::now()->diffInSeconds($check->first()->created_at, true);
            if ($difference > (int)Settings::group('otp')->get('otp_expire_time') * 60) {
                return new JsonResponse([
                    'errors' => ['code' => [trans('all.message.code_is_expired')]]
                ], 400);
            }

            return new JsonResponse([
                'message' => trans('all.message.you_can_reset_your_password')
            ], 200);
        } else {
            return new JsonResponse([
                'errors' => ['code' => [trans('all.message.code_is_invalid')]]
            ], 400);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'            => ['required', 'string', 'email', 'max:200'],
            'password'         => ['required', 'string', 'min:6', 'required_with:confirm_password'],
            'confirm_password' => ['required', 'string', 'min:6', 'same:password'],
            'token'            => env('DEMO') || Settings::group('site')->get('site_email_verification') == Activity::DISABLE ? ['nullable'] : ['required']
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->errors()], 422);
        }

        if (!env('DEMO') && Settings::group('site')->get('site_email_verification') == Activity::ENABLE) {
            $passwordRestToken = DB::table('password_reset_tokens')->where([
                ['email', $request->post('email')],
                ['token', $request->post('token')],
            ]);
            if (!$passwordRestToken->exists()) {
                return new JsonResponse([
                    'errors' => ['token' => [trans('all.message.token_is_invalid')]]
                ], 400);
            }


            $user = User::where(['email' => $request->post('email')]);
            if ($user->exists()) {
                $passwordRestToken->delete();
                $user->update([
                    'password' => Hash::make($request->post('password'))
                ]);
            }
        } else {
            $user = User::where(['email' => $request->post('email')]);
            if ($user->exists()) {
                $user->update([
                    'password' => Hash::make($request->post('password'))
                ]);
            }
        }

        return new JsonResponse([
            'message' => "Your password has been reset"
        ], 200);
    }
}
