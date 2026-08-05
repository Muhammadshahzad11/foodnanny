<?php

namespace App\Services;


use Exception;
use Carbon\Carbon;
use App\Enums\OtpType;
use App\Events\OtpRequested;
use Illuminate\Http\Request;
use App\Models\OneTimePassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\VerifyPhoneRequest;
use Dipokhalder\Settings\Facades\Settings;

class OtpManagerService
{

    /**
     * @throws Exception
     */
    public function phoneOTP(Request $request): string
    {
        try {
            $otp = DB::table('one_time_passwords')->where([
                ['provider', $request->post('phone')], ['code', $request->post('code')]
            ]);

            if ($otp->exists()) {
                $otp->delete();
            }

            if (Settings::group('otp')->get('otp_type') == OtpType::SMS || Settings::group('otp')->get('otp_type') == OtpType::BOTH) {
                $token = rand(
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit')) - 1
                );
            } else {
                $token = rand(pow(10, 4 - 1), pow(10, 4) - 1);
            }

            $otp = OneTimePassword::create([
                'provider'   => $request->phone,
                'code'       => $request->code,
                'token'      => $token,
                'created_at' => now(),
            ]);

            if (!blank($otp)) {
                if (!env('DEMO')) {
                    OtpRequested::dispatch(
                        ['phone' => $request->post('phone'), 'code' => $request->post('code'), 'token' => $token]
                    );
                }
            }

            return (string) $token;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function phoneVerify(VerifyPhoneRequest $request, $delete = true): true
    {
        try {
            $otp = DB::table('one_time_passwords')->where(['code' => $request->post('code'), 'provider' => $request->post('phone'), 'token' => $request->post('token')]);
            if ($otp->exists()) {
                $difference = (int) Carbon::now()->diffInSeconds($otp->first()->created_at,true);
                if ($difference > (int)Settings::group('otp')->get('otp_expire_time') * 60) {
                    throw new Exception(trans('all.message.code_is_expired'), 422);
                } else {
                    if ($delete) {
                        $otp->delete();
                    }
                    return true;
                }
            } else {
                throw new Exception(trans('all.message.code_is_invalid'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
