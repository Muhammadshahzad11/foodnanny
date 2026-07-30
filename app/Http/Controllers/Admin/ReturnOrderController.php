<?php

namespace App\Http\Controllers\Admin;


use Exception;
use App\Models\Order;
use App\Services\OrderService;
use App\Exports\ReturnOrderExport;
use App\Services\ReturnOrderService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ReturnOrderRequest;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ReturnOrderController extends AdminController implements HasMiddleware
{
    public OrderService $orderService;
    public ReturnOrderService $returnOrderService;

    public function __construct(ReturnOrderService $returnOrderService)
    {
        parent::__construct();
        $this->returnOrderService = $returnOrderService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:return-orders', only: ['index', 'export']),
            new Middleware('permission:return-orders_create', only: ['store']),
            new Middleware('permission:return-orders_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderResource::collection($this->returnOrderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ReturnOrderRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|OrderResource
    {
        try {
            return new OrderResource($this->returnOrderService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Order $order): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->returnOrderService->show($order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReturnOrderExport($this->returnOrderService, $request), 'ReturnOrder.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Order $order): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->returnOrderService->destroy($order);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
