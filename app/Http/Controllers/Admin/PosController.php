<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\OrderService;
use App\Services\KotRoutingService;
use App\Services\RestaurantTableService;
use App\Http\Requests\PosOrderRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\RestaurantTableResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PosController extends AdminController implements HasMiddleware
{
    private OrderService $orderService;

    public function __construct(
        OrderService $order,
        protected RestaurantTableService $restaurantTableService,
        protected KotRoutingService $kotRoutingService
    ) {
        parent::__construct();
        $this->orderService = $order;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:pos', only: ['store', 'tables', 'printers']),
        ];
    }

    public function store(PosOrderRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $order = $this->orderService->posOrderStore($request);
            $print = $this->kotRoutingService->processPosOrder($order);

            return (new OrderDetailsResource($order))->additional([
                'print_jobs' => $print['jobs'],
                'kot_count'  => $print['kot_count'],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Auto-fetch restaurant printers with live IP connection status for POS/KOT.
     */
    public function printers(\Illuminate\Http\Request $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $format = $request->get('format'); // kot|invoice|null

            return \App\Http\Resources\PrinterResource::collection(
                app(\App\Services\PrinterService::class)->fetchConnected(is_string($format) ? $format : null)
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Tables for POS dine-in (available to any user with POS permission).
     */
    public function tables(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $request->merge(['paginate' => 0]);

            return RestaurantTableResource::collection($this->restaurantTableService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
