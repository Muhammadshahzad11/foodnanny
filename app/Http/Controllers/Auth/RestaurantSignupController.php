<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Apply;
use App\Enums\Status;
use App\Http\Requests\RestaurantInfoRequest;
use App\Http\Requests\RestaurantOwnerSignupRequest;
use App\Models\OrderSetup;
use Exception;
use App\Enums\Ask;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\Activity;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use App\Enums\Role as EnumRole;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Support\Facades\DB;
use App\Services\OtpManagerService;
use App\Services\ZoneService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\SignupPhoneRequest;
use App\Http\Requests\VerifyPhoneRequest;


class RestaurantSignupController extends Controller
{

    private OtpManagerService $otpManagerService;

    public function __construct(OtpManagerService $otpManagerService)
    {
        $this->otpManagerService = $otpManagerService;
    }

    public function phone(SignupPhoneRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $user = User::where(['country_code' => $request->post('code'), 'phone' => $request->post('phone')])->first();
            if ($user) {
                return response(['status' => false, 'message' => trans("all.message.phone_exist")], 422);
            } else {
                $payload = ['status' => true, 'message' => trans("all.message.check_your_phone_for_code")];
                if (Settings::group('site')->get('site_phone_verification') == Activity::ENABLE) {
                    $otp = $this->otpManagerService->phoneOTP($request);
                    if (OtpManagerService::shouldExposeOtp()) {
                        $payload['otp'] = $otp;
                    }
                }
                return response($payload, 200);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(VerifyPhoneRequest $request): \Illuminate\Http\Response|array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->otpManagerService->phoneVerify($request, false);
            return response(['status' => true, 'message' => trans("all.message.otp_verify_success")], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function owner(RestaurantOwnerSignupRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response(['status' => true, 'message' => trans("all.message.restaurant_owner_register_successfully")], 200);
    }


    /**
     * @throws Exception
     */
    public function register(RestaurantInfoRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name'                 => $request->post('owner_name'),
                    'username'             => Str::slug($request->post('owner_name')) . AppLibrary::timeWithRand(),
                    'email'                => $request->post('owner_email'),
                    'country_code'         => $request->post('owner_country_code'),
                    'phone'                => $request->post('owner_phone'),
                    'restaurant_id'        => 0,
                    'email_verified_at'    => Carbon::now()->getTimestamp(),
                    'is_guest'             => Ask::NO,
                    'password'             => Hash::make($request->post('owner_password')),
                    // Pending admin approval — owner cannot login until restaurant is approved
                    'status'               => Status::INACTIVE,
                    'terms_and_conditions' => $request->post('page_id') > 0 ? $request->post('terms_and_conditions') : Ask::NO
                ]);
                $user->assignRole(EnumRole::RESTAURANT_OWNER);

                $restaurant = Restaurant::create([
                    'name'                 => $request->post('restaurant_name'),
                    'slug'                 => Str::slug($request->post('restaurant_name')),
                    'email'                => $request->post('restaurant_email'),
                    'country_code'         => $request->post('restaurant_country_code'),
                    'phone'                => $request->post('restaurant_phone'),
                    'address'              => $request->post('restaurant_address'),
                    'city'                 => $request->post('restaurant_city'),
                    'state'                => $request->post('restaurant_state'),
                    'zip_code'             => $request->post('restaurant_zip_code'),
                    'latitude'             => $request->post('restaurant_latitude') ?: null,
                    'longitude'            => $request->post('restaurant_longitude') ?: null,
                    'status'               => Status::INACTIVE,
                    'current_status'       => Status::INACTIVE,
                    'apply'                => Apply::RESTAURANT_OWNER,
                    'terms_and_conditions' => $request->post('page_id') > 0 ? $request->post('terms_and_conditions') : Ask::NO
                ]);

                $zoneId = app(ZoneService::class)->applyDetectedZoneId(
                    $restaurant->latitude !== null ? (float) $restaurant->latitude : null,
                    $restaurant->longitude !== null ? (float) $restaurant->longitude : null
                );
                $restaurant->zone_id = $zoneId;
                $restaurant->user_id = $user->id;
                $restaurant->save();

                $user->restaurant_id = $restaurant->id;
                $user->zone_id = $zoneId;
                $user->save();

                OrderSetup::create([
                    'restaurant_id'                => $restaurant->id,
                    'food_preparation_time'        => 30,
                    'schedule_order_slot_duration' => 15,
                    'takeaway'                     => Activity::ENABLE,
                    'delivery'                     => Activity::ENABLE,
                    'minimum_order_limit'          => 1
                ]);

                app(\App\Services\TimeSlotService::class)->ensureDefaults($restaurant->id);
            });
            return response([
                'status'  => true,
                'message' => trans('all.message.restaurant_register_pending_approval'),
                'pending' => true,
            ], 200);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
