<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\OrderSetupService;
use App\Http\Requests\OrderSetupRequest;
use App\Http\Resources\OrderSetupResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OrderSetupController extends AdminController implements HasMiddleware
{
    public OrderSetupService $orderSetupService;

    public function __construct(OrderSetupService $orderSetupService)
    {
        parent::__construct();
        $this->orderSetupService = $orderSetupService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Http\Response | OrderSetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderSetupResource($this->orderSetupService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OrderSetupRequest $request): \Illuminate\Http\Response | OrderSetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderSetupResource($this->orderSetupService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
