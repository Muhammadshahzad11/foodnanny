<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Enums\Owner;
use App\Models\Coupon;
use App\Enums\Discount;
use App\Models\Restaurant;
use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use App\Models\FrontendCoupon;
use Illuminate\Support\Facades\DB;
use App\Models\FrontendOrderCoupon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CouponRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TranslationRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\CouponCheckRequest;

class CouponService
{
    public object $coupon;
    protected array $couponFilter = [
        'name',
        'code',
        'discount',
        'discount_type',
        'start_date',
        'end_date',
        'minimum_order',
        'maximum_discount',
        'limit_per_user'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Coupon::where(['owner' => Owner::RESTAURANT_OWNER])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->couponFilter)) {
                        if ($key == "start_date") {
                            $start_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '>=', $start_date);
                        } else if ($key == "end_date") {
                            $end_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '<=', $end_date);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(CouponRequest $request)
    {
        try {
            $this->coupon = Coupon::create([
                'name'             => $request->name,
                'description'      => $request->description,
                'code'             => $request->code,
                'discount'         => $request->discount,
                'discount_type'    => $request->discount_type,
                'start_date'       => !blank($request->start_date) ? date('Y-m-d H:i:s',strtotime($request->start_date)) : "",
                'end_date'         => !blank($request->end_date) ? date('Y-m-d H:i:s',strtotime($request->end_date)) : "",
                'minimum_order'    => $request->minimum_order,
                'maximum_discount' => $request->discount_type == DiscountType::FIXED ? 0 : $request->maximum_discount,
                'limit_per_user'   => $request->limit_per_user,
                'type'             => Discount::DEFAULT,
                'owner'            => Owner::RESTAURANT_OWNER
            ]);
            return $this->coupon;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CouponRequest $request, Coupon $coupon): Coupon
    {
        try {
            DB::transaction(function () use ($request, $coupon) {
                $coupon->name             = $request->name;
                $coupon->description      = $request->description;
                $coupon->code             = $request->code;
                $coupon->discount         = $request->discount;
                $coupon->discount_type    = $request->discount_type;
                $coupon->start_date       = !blank($request->start_date) ? date('Y-m-d H:i:s',strtotime($request->start_date)) : null;
                $coupon->end_date         = !blank($request->end_date) ? date('Y-m-d H:i:s',strtotime($request->end_date)) : null;
                $coupon->minimum_order    = $request->minimum_order;
                $coupon->maximum_discount = $request->discount_type == DiscountType::FIXED ? 0 : $request->maximum_discount;
                $coupon->limit_per_user   = $request->limit_per_user;
                $coupon->save();
            });
            return $coupon;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Coupon $coupon): void
    {
        try {
            $coupon->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Coupon $coupon): Coupon
    {
        try {
            return $coupon->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, Coupon $coupon): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $coupon->translations()->updateOrCreate(
                        ['locale' => $locale, 'key' => $key],
                        ['value' => $value ?? '']
                    );
                }
            }
            return response(['message' => trans('all.message.translations_saved_successfully')], 200);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function frontendShow(FrontendCoupon $frontendCoupon): FrontendCoupon
    {
        try {
            return $frontendCoupon->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function couponDateWise(Restaurant $restaurant)
    {
        try {
            return FrontendCoupon::with('translations')->where(['restaurant_id' => $restaurant->id, 'owner' => Owner::RESTAURANT_OWNER])->orWhere(['restaurant_id' => 0, 'owner' => Owner::ADMIN])->get()->filter(function ($coupon) {
                if (AppLibrary::isBetweenDate($coupon->start_date, $coupon->end_date)) {
                    return $coupon;
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function couponChecking(Restaurant $restaurant, CouponCheckRequest $request)
    {
        try {
            $coupon = FrontendCoupon::with('translations')->where(['restaurant_id' => $restaurant->id, 'owner' => Owner::RESTAURANT_OWNER, 'code' => $request->code])->first();
            if(!$coupon) {
                $coupon = FrontendCoupon::with('translations')->where(['restaurant_id' => 0, 'owner' => Owner::ADMIN, 'code' => $request->code])->first();
            }

            if ($coupon) {
                if ($coupon->minimum_order > $request->total) {
                    throw new Exception(trans('all.message.minimum_order_amount') . AppLibrary::currencyAmountFormat($coupon->minimum_order), 422);
                } else {
                    if (strtotime($coupon->end_date) >= strtotime(Carbon::now())) {
                        $oldCoupon = FrontendOrderCoupon::where(['coupon_id' => $coupon->id, 'user_id' => Auth::user()->id])->get();
                        if(count($oldCoupon) >= $coupon->limit_per_user) {
                            throw new Exception(trans('all.message.coupon_user_limit_expired'), 422);
                        } else {
                            return $coupon;
                        }
                    } else {
                        throw new Exception(trans('all.message.coupon_date_expired'), 422);
                    }
                }
            } else {
                throw new Exception(trans('all.message.coupon_not_exist'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
