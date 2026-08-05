<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\Offer;
use App\Enums\OfferStatus;
use App\Models\OfferRestaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\OfferRestaurantRequest;
use App\Http\Requests\OfferRestaurantVerifyRequest;

class OfferRestaurantService
{
    protected $offerRestaurantFilter = [
        'restaurant_id',
        'apply',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request, Offer $offer)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return OfferRestaurant::with('offer', 'restaurant')->where(['offer_id' => $offer->id])->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->offerRestaurantFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store(OfferRestaurantRequest $request, Offer $offer)
    {
        try {
            if ($offer->is_single === Ask::NO) {
                return OfferRestaurant::create($request->validated() + ['offer_id' => $offer->id, 'apply' => Ask::YES, 'status' => OfferStatus::APPROVE]);
            } else {
                throw new Exception(trans('all.message.restaurant_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Offer $offer, OfferRestaurant $offerRestaurant)
    {
        try {
            if ($offer->id == $offerRestaurant->offer_id) {
                $offerRestaurant->delete();
            } else {
                throw new Exception(trans('all.message.restaurant_match'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function verify(OfferRestaurantVerifyRequest $request, Offer $offer, OfferRestaurant $offerRestaurant): OfferRestaurant
    {
        try {
            DB::transaction(function () use ($request, $offer, $offerRestaurant) {
                if ($offer->id === $offerRestaurant->offer_id) {
                    $offerRestaurant->status = $request->status;
                    $offerRestaurant->save();
                } else {
                    throw new Exception(trans('all.message.restaurant_match'), 422);
                }
            });
            return $offerRestaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
