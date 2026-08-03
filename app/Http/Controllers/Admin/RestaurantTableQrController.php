<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RestaurantTableQrBulkRequest;
use App\Http\Resources\RestaurantTableQrResource;
use App\Models\RestaurantTable;
use App\Services\RestaurantTableQrService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RestaurantTableQrController extends AdminController implements HasMiddleware
{
    public function __construct(
        protected RestaurantTableQrService $restaurantTableQrService
    ) {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:table_qr_view', only: ['preview', 'printData']),
            new Middleware('permission:table_qr_generate', only: ['generate', 'generateMissing', 'generateAll']),
            new Middleware('permission:table_qr_download', only: ['download', 'bulkDownload']),
            new Middleware('permission:table_qr_regenerate', only: ['regenerate', 'regenerateSelected']),
        ];
    }

    public function preview(RestaurantTable $restaurantTable)
    {
        try {
            return new RestaurantTableQrResource($this->restaurantTableQrService->preview($restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generate(RestaurantTable $restaurantTable)
    {
        try {
            return new RestaurantTableQrResource($this->restaurantTableQrService->generate($restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function regenerate(RestaurantTable $restaurantTable)
    {
        try {
            return new RestaurantTableQrResource($this->restaurantTableQrService->regenerate($restaurantTable));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function download(Request $request, RestaurantTable $restaurantTable)
    {
        try {
            return $this->restaurantTableQrService->download(
                $restaurantTable,
                $request->get('format', 'png')
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generateMissing()
    {
        try {
            $tables = $this->restaurantTableQrService->generateMissing();

            return RestaurantTableQrResource::collection($tables)->additional([
                'message' => trans('all.message.table_qr_generated'),
                'count'   => $tables->count(),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function generateAll()
    {
        try {
            $tables = $this->restaurantTableQrService->generateAll();

            return RestaurantTableQrResource::collection($tables)->additional([
                'message' => trans('all.message.table_qr_generated'),
                'count'   => $tables->count(),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function regenerateSelected(RestaurantTableQrBulkRequest $request)
    {
        try {
            $tables = $this->restaurantTableQrService->regenerateSelected($request->validated('ids'));

            return RestaurantTableQrResource::collection($tables)->additional([
                'message' => trans('all.message.table_qr_regenerated'),
                'count'   => $tables->count(),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function bulkDownload(RestaurantTableQrBulkRequest $request)
    {
        try {
            return $this->restaurantTableQrService->bulkDownload($request->validated('ids'));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function printData(RestaurantTableQrBulkRequest $request)
    {
        try {
            return response()->json([
                'data' => $this->restaurantTableQrService->printData($request->validated('ids')),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
