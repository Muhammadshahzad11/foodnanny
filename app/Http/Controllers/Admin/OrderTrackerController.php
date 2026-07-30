<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderTrackerRequest;
use Exception;
use App\Services\OrderService;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Routing\Controllers\Middleware;

class OrderTrackerController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private OrderService $orderService;

    public function __construct(OrderService $order)
    {
        parent::__construct();
        $this->orderService = $order;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:order-tracker', only: ['index'])
        ];
    }

    public function index(OrderTrackerRequest $orderTrackerRequest): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $data = $this->orderService->fetchByOrderSerialNo($orderTrackerRequest);
            if (!blank($data)) {
                return new OrderDetailsResource($data);
            } else {
                return response([
                    'status'  => false,
                    'message' => trans('all.message.order_not_found'),
                    'errors'  => [
                        'order_id' => [trans('all.message.order_not_found')]
                    ]
                ], 422);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
