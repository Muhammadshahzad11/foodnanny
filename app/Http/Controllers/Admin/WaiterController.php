<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\WaiterOrderRequest;
use App\Http\Requests\WaiterOrderUpdateRequest;
use App\Http\Resources\WaiterOrderResource;
use App\Http\Resources\WaiterTableResource;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Services\KitchenOrderService;
use App\Services\WaiterOrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WaiterController extends AdminController implements HasMiddleware
{
    public function __construct(
        protected WaiterOrderService $waiterOrderService,
        protected KitchenOrderService $kitchenOrderService
    ) {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:waiter|waiter_dashboard', only: ['dashboard']),
            new Middleware('permission:waiter|waiter_tables', only: ['tables', 'tableShow']),
            new Middleware('permission:waiter|waiter_orders', only: ['orders', 'orderShow']),
            new Middleware('permission:waiter_orders_create', only: ['store']),
            new Middleware('permission:waiter_orders_edit', only: ['update']),
            new Middleware('permission:waiter_orders_send', only: ['sendToKitchen', 'printData']),
            new Middleware('permission:waiter_orders_cancel_draft', only: ['cancelDraft']),
        ];
    }

    public function dashboard(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
    {
        try {
            return response(['data' => $this->waiterOrderService->dashboard()]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function tables(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return WaiterTableResource::collection($this->waiterOrderService->tables($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function tableShow(RestaurantTable $restaurantTable): \Illuminate\Http\Response|WaiterTableResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new WaiterTableResource($this->waiterOrderService->tableShow($restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orders(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return WaiterOrderResource::collection($this->waiterOrderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orderShow(Order $order): \Illuminate\Http\Response|WaiterOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new WaiterOrderResource($this->waiterOrderService->show($order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(WaiterOrderRequest $request): \Illuminate\Http\Response|WaiterOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new WaiterOrderResource($this->waiterOrderService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(WaiterOrderUpdateRequest $request, Order $order): \Illuminate\Http\Response|WaiterOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new WaiterOrderResource($this->waiterOrderService->update($request, $order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sendToKitchen(Request $request, Order $order): \Illuminate\Http\Response|WaiterOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new WaiterOrderResource(
                $this->waiterOrderService->sendToKitchen($order, $request->input('updated_at'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function cancelDraft(Order $order): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->waiterOrderService->cancelDraft($order);

            return response(['status' => true, 'message' => trans('all.message.waiter_draft_canceled')]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function printData(Order $order): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->kitchenOrderService->printData($order);

            return response([
                'data' => [
                    'ticket_no'   => $result['ticket']->ticket_no,
                    'print_count' => $result['ticket']->print_count,
                    'printed_at'  => $result['ticket']->printed_at,
                    'payload'     => $result['payload'],
                ],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
