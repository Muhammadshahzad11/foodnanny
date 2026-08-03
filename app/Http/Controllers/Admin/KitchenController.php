<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Resources\KitchenOrderResource;
use App\Models\Order;
use App\Services\KitchenOrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KitchenController extends AdminController implements HasMiddleware
{
    public function __construct(protected KitchenOrderService $kitchenOrderService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:kitchen|kitchen_dashboard', only: ['dashboard']),
            new Middleware('permission:kitchen|kitchen_view', only: ['orders', 'orderShow']),
            new Middleware('permission:kitchen_accept', only: ['accept']),
            new Middleware('permission:kitchen_prepare', only: ['preparing']),
            new Middleware('permission:kitchen_ready', only: ['ready']),
            new Middleware('permission:kitchen_print', only: ['printData']),
            new Middleware('permission:kitchen_accept|kitchen_prepare', only: ['priority']),
        ];
    }

    public function dashboard(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
    {
        try {
            return response(['data' => $this->kitchenOrderService->dashboard()]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orders(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return KitchenOrderResource::collection($this->kitchenOrderService->queue($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orderShow(Order $order): \Illuminate\Http\Response|KitchenOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new KitchenOrderResource($this->kitchenOrderService->show($order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function accept(Request $request, Order $order): \Illuminate\Http\Response|KitchenOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new KitchenOrderResource(
                $this->kitchenOrderService->accept($order, $request->input('updated_at'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function preparing(Request $request, Order $order): \Illuminate\Http\Response|KitchenOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new KitchenOrderResource(
                $this->kitchenOrderService->preparing($order, $request->input('updated_at'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function ready(Request $request, Order $order): \Illuminate\Http\Response|KitchenOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new KitchenOrderResource(
                $this->kitchenOrderService->ready($order, $request->input('updated_at'))
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function priority(Request $request, Order $order): \Illuminate\Http\Response|KitchenOrderResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $request->validate(['kitchen_priority' => ['required', 'integer', 'min:0', 'max:100']]);

            return new KitchenOrderResource(
                $this->kitchenOrderService->updatePriority($order, (int) $request->input('kitchen_priority'))
            );
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
