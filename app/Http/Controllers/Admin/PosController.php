<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\OrderService;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PosController extends AdminController implements HasMiddleware
{
    private OrderService $orderService;

    public function __construct(OrderService $order)
    {
        parent::__construct();
        $this->orderService = $order;
    }
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pos', only: ['store'])
        ];
    }

    public function store(PosOrderRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->posOrderStore($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
