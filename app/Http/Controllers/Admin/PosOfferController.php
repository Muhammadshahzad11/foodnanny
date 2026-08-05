<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Enums\Status;
use App\Http\Resources\CheckOfferResource;
use App\Models\Offer;
use App\Traits\DefaultAccessModelTrait;
use Exception;

class PosOfferController extends AdminController
{

    use DefaultAccessModelTrait;

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|CheckOfferResource
    {
        try {
            $data = Offer::whereHas('offerRestaurants', function ($query) {
                $query->where(['status' => OfferStatus::APPROVE, 'restaurant_id' => $this->restaurant()]);
            })->where(function ($query) {
                $query->whereDate('start_date', '<=', Date('Y-m-d'));
                $query->whereDate('end_date', '>=', Date('Y-m-d'));
                $query->whereTime('start_time', '<=', Date('H:i:s'));
                $query->whereTime('end_time', '>=', Date('H:i:s'));
            })->where(['status' => Status::ACTIVE])->first();
            return $data == null ? response(['data' => (object)[]]) : new CheckOfferResource($data);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
