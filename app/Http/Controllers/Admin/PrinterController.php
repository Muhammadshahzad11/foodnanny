<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\PrinterRequest;
use App\Http\Resources\PrinterResource;
use App\Models\Printer;
use App\Services\PrinterService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PrinterController extends AdminController implements HasMiddleware
{
    public function __construct(protected PrinterService $printerService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:restaurant-settings', only: ['index', 'store', 'update', 'destroy', 'testPrint', 'fetchStatus']),
        ];
    }

    public function index(PaginateRequest $request)
    {
        try {
            return PrinterResource::collection($this->printerService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function fetchStatus(PaginateRequest $request)
    {
        try {
            $request->merge(['with_connection' => 1, 'paginate' => 0]);

            return PrinterResource::collection($this->printerService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(PrinterRequest $request)
    {
        try {
            return new PrinterResource($this->printerService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PrinterRequest $request, Printer $printer)
    {
        try {
            return new PrinterResource($this->printerService->update($request, $printer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Printer $printer)
    {
        try {
            $this->printerService->destroy($printer);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function testPrint(Printer $printer)
    {
        try {
            return response(['status' => true, 'data' => $this->printerService->testPrint($printer)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
