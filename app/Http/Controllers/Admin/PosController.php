<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\RestaurantTableResource;
use App\Models\Order;
use App\Services\KotRoutingService;
use App\Services\OrderService;
use App\Services\PosRunningOrderService;
use App\Services\RestaurantTableService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PosController extends AdminController implements HasMiddleware
{
    private OrderService $orderService;

    public function __construct(
        OrderService $order,
        protected RestaurantTableService $restaurantTableService,
        protected KotRoutingService $kotRoutingService,
        protected PosRunningOrderService $posRunningOrderService
    ) {
        parent::__construct();
        $this->orderService = $order;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:pos', only: [
                'store',
                'tables',
                'printers',
                'openOrders',
                'openOrderForTable',
                'updateOpenOrder',
                'printBill',
                'orderHistory',
                'updateTableStatus',
                'customers',
                'storeCustomer',
            ]),
            // POS screen + POS Orders view can take payment / close open orders
            new Middleware('permission:pos|pos-orders_show', only: [
                'payOpenOrder',
            ]),
        ];
    }

    public function store(PosOrderRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $orderType  = (int) $request->input('order_type');
            $placeOnly  = $request->boolean('place_only')
                || ($orderType === \App\Enums\OrderType::DINING_TABLE && !$request->boolean('close_with_payment'));

            // Dine-in Place Order: save + KOT only, leave open/unpaid
            if ($placeOnly && $orderType === \App\Enums\OrderType::DINING_TABLE) {
                $tableId  = (int) $request->input('table_id');
                $existing = $tableId > 0 ? $this->posRunningOrderService->openOrderForTable($tableId) : null;
                if ($existing) {
                    $result = $this->posRunningOrderService->updateOpenOrder($existing, $request);
                } else {
                    $result = $this->posRunningOrderService->placeDineIn($request);
                }
                $order = $result['order'];
                $print = $result['print'];

                return (new OrderDetailsResource($order))->additional([
                    'print_jobs' => $print['jobs'] ?? [],
                    'kot_count'  => $print['kot_count'] ?? 0,
                    'warnings'   => $print['warnings'] ?? [],
                    'open_order' => true,
                ]);
            }

            $order = $this->orderService->posOrderStore($request);

            $printInvoice = true;
            // Delivery: KOT on place; invoice still allowed when paid at counter (default)
            if ($orderType === \App\Enums\OrderType::DELIVERY && $request->boolean('kot_only')) {
                $printInvoice = false;
            }

            $print = $this->kotRoutingService->processPosOrder($order, [
                'print_kot'     => true,
                'print_invoice' => $printInvoice,
            ]);

            return (new OrderDetailsResource($order))->additional([
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => $print['kot_count'] ?? 0,
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('POS store failed: '.$exception->getMessage(), ['exception' => $exception]);
            return response(['status' => false, 'message' => $exception->getMessage() ?: 'Could not place order.'], 422);
        }
    }

    /**
     * Running / open unpaid POS orders.
     */
    public function openOrders(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderDetailsResource::collection($this->posRunningOrderService->listOpenOrders());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Open existing order for an occupied table (do not create duplicate).
     */
    public function openOrderForTable(int $tableId): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $order = $this->posRunningOrderService->openOrderForTable($tableId);
            if (!$order) {
                return response(['status' => false, 'message' => 'No open order for this table.'], 404);
            }

            return new OrderDetailsResource($order);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function updateOpenOrder(PosOrderRequest $request, Order $order): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $result = $this->posRunningOrderService->updateOpenOrder($order, $request);
            $print  = $result['print'];

            return (new OrderDetailsResource($result['order']))->additional([
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => $print['kot_count'] ?? 0,
                'warnings'   => $print['warnings'] ?? [],
                'changes'    => $result['changes'],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function printBill(Order $order): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $result = $this->posRunningOrderService->printBill($order);
            $print  = $result['print'];

            return (new OrderDetailsResource($result['order']))->additional([
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => 0,
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function payOpenOrder(PosOrderRequest $request, Order $order): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $result = $this->posRunningOrderService->payAndClose(
                $order,
                $request,
                !$request->boolean('skip_invoice')
            );
            $print = $result['print'];

            return (new OrderDetailsResource($result['order']))->additional([
                'print_jobs' => $print['jobs'] ?? [],
                'kot_count'  => 0,
                'warnings'   => $print['warnings'] ?? [],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orderHistory(Order $order): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response()->json([
                'data' => $this->posRunningOrderService->history($order),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Auto-fetch restaurant printers with live IP connection status for POS/KOT.
     */
    public function printers(Request $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
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

    /**
     * Change table status from POS (Available / Occupied / Reserved / Cleaning).
     */
    public function updateTableStatus(\Illuminate\Http\Request $request, int $tableId): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $status = (int) $request->input('status');
            $table  = $this->posRunningOrderService->updateTableStatus($tableId, $status);

            return (new RestaurantTableResource($table))->response();
        } catch (\Throwable $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    /**
     * Create/update a delivery customer from POS (name, phone, address).
     */
    public function storeCustomer(\Illuminate\Http\Request $request): \Illuminate\Http\Response|\App\Http\Resources\PosCustomerResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $customer = app(\App\Services\CustomerService::class)->storeFromPos(
                $request->only(['name', 'phone', 'address', 'country_code'])
            );

            return new \App\Http\Resources\PosCustomerResource($customer);
        } catch (\Throwable $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Customer list for POS Delivery (select previous customers).
     */
    public function customers(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $request->merge(['paginate' => 0, 'order_column' => 'name', 'order_type' => 'asc']);

            $customers = app(\App\Services\CustomerService::class)->allCustomer($request);
            if (method_exists($customers, 'loadMissing')) {
                $customers->loadMissing('addresses');
            }

            return \App\Http\Resources\PosCustomerResource::collection($customers);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
