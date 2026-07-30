<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AvailableOrderResource;
use Exception;
use App\Models\Order;
use App\Exports\AvailableOrderExport;
use App\Services\AvailableOrderService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class AvailableOrderController extends AdminController implements HasMiddleware
{
    private AvailableOrderService $availableOrderService;

    public function __construct(AvailableOrderService $availableOrderService)
    {
        parent::__construct();
        $this->availableOrderService = $availableOrderService;
    }
    public static function middleware(): array
    {
        return [
            new Middleware('permission:available-orders', only: ['index', 'export', 'changeStatus'])
        ];
    }
    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AvailableOrderResource::collection($this->availableOrderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new AvailableOrderExport($this->availableOrderService, $request), 'Available-Order.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(Order $order): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->availableOrderService->changeStatus($order);
            return response(['status' => true], 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
