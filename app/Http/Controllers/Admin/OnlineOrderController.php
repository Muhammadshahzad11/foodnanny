<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Order;
use App\Exports\OrderExport;
use App\Services\OrderService;
use App\Services\KotRoutingService;
use App\Traits\DefaultAccessModelTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OrderTokenRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OnlineOrderController extends AdminController implements HasMiddleware
{
    use DefaultAccessModelTrait;

    private OrderService $orderService;

    public function __construct(
        OrderService $order,
        protected KotRoutingService $kotRoutingService
    ) {
        parent::__construct();
        $this->orderService = $order;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:online-orders', only: [
                'index',
                'show',
                'export',
                'changeStatus',
                'addToken',
                'printInvoice',
                'printKot',
                'printBoth',
            ]),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderResource::collection($this->orderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Order $order): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->show($order, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new OrderExport($this->orderService, $request), 'Online-Order.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(Order $order, OrderStatusRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->changeStatus($order, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function addToken(Order $order, OrderTokenRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->addToken($order, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Reprint customer invoice / bill for an online order.
     */
    public function printInvoice(Order $order): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->assertRestaurantAccess($order);
            $print = $this->kotRoutingService->processInvoiceOnly($order);

            return response([
                'data'       => ['order_id' => $order->id],
                'print_jobs' => $print['jobs'] ?? [],
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Reprint kitchen KOT slip(s) for an online order.
     */
    public function printKot(Order $order): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->assertRestaurantAccess($order);
            $print = $this->kotRoutingService->processPosOrder($order, [
                'print_kot'     => true,
                'print_invoice' => false,
            ]);

            return response([
                'data'       => ['order_id' => $order->id],
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => $print['kot_count'] ?? 0,
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Print both KOT and customer invoice for an online order.
     */
    public function printBoth(Order $order): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->assertRestaurantAccess($order);
            $print = $this->kotRoutingService->processPosOrder($order, [
                'print_kot'     => true,
                'print_invoice' => true,
            ]);

            return response([
                'data'       => ['order_id' => $order->id],
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => $print['kot_count'] ?? 0,
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertRestaurantAccess(Order $order): void
    {
        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && (int) $order->restaurant_id !== $scoped) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }
}
