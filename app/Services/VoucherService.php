<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Enums\Owner;
use App\Enums\Discount;
use App\Models\Voucher;
use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\VoucherRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TranslationRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\CouponCheckRequest;

class VoucherService
{
    public $voucher;
    protected $voucherFilter = [
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

    protected $exceptFilter = [
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

            return Voucher::where(['restaurant_id' => 0, 'owner' => Owner::ADMIN])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->voucherFilter)) {
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
    public function store(VoucherRequest $request)
    {
        try {
            $this->voucher = Voucher::create([
                'restaurant_id'    => 0,
                'name'             => $request->name,
                'description'      => $request->description,
                'code'             => $request->code,
                'discount'         => $request->type == Discount::FREE_DELIVERY ? 0 : $request->discount,
                'discount_type'    => $request->type == Discount::FREE_DELIVERY ? DiscountType::PERCENTAGE : $request->discount_type,
                'start_date'       => !blank($request->start_date) ? date('Y-m-d H:i:s', strtotime($request->start_date)) : "",
                'end_date'         => !blank($request->end_date) ? date('Y-m-d H:i:s',strtotime($request->end_date)) : "",
                'minimum_order'    => $request->minimum_order,
                'maximum_discount' => $request->type == Discount::FREE_DELIVERY || $request->discount_type == DiscountType::FIXED ? 0 : $request->maximum_discount,
                'limit_per_user'   => $request->limit_per_user,
                'type'             => $request->type,
                'owner'            => Owner::ADMIN
            ]);
            return $this->voucher;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(VoucherRequest $request, Voucher $voucher)
    {
        try {
            DB::transaction(function () use ($request, $voucher) {
                $voucher->name             = $request->name;
                $voucher->description      = $request->description;
                $voucher->code             = $request->code;
                $voucher->discount         = $request->type == Discount::FREE_DELIVERY ? 0 : $request->discount;
                $voucher->discount_type    = $request->type == Discount::FREE_DELIVERY ? DiscountType::PERCENTAGE : $request->discount_type;
                $voucher->start_date       = !blank($request->start_date) ? date('Y-m-d H:i:s',strtotime($request->start_date)) : null;
                $voucher->end_date         = !blank($request->end_date) ? date('Y-m-d H:i:s',strtotime($request->end_date)) : null;
                $voucher->minimum_order    = $request->minimum_order;
                $voucher->maximum_discount = $request->type == Discount::FREE_DELIVERY || $request->discount_type == DiscountType::FIXED ? 0 : $request->maximum_discount;
                $voucher->limit_per_user   = $request->limit_per_user;
                $voucher->type             = $request->type;
                $voucher->save();
            });
            return $voucher;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Voucher $voucher): Voucher
    {
        try {
            return $voucher->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, Voucher $voucher): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $voucher->translations()->updateOrCreate(
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
    public function voucherDateWise(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Voucher::where(['restaurant_id' => 0, 'owner' => Owner::ADMIN])->get()->filter(function ($voucher) {
                if (AppLibrary::isBetweenDate($voucher->start_date, $voucher->end_date)) {
                    return $voucher;
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
    public function voucherChecking(CouponCheckRequest $request)
    {
        try {
            $voucher = Voucher::where(['restaurant_id' => 0, 'owner' => Owner::ADMIN, 'code' => $request->code])->first();
            if ($voucher) {
                if ($voucher->minimum_order > $request->total) {
                    throw new Exception(trans('all.message.minimum_order_amount') . AppLibrary::convertAmountFormat($voucher->minimum_order), 422);
                } else {
                    if (strtotime($voucher->end_date) >= strtotime(Carbon::now())) {
                        return $voucher;
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
