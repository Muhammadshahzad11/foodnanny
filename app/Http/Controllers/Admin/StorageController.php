<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\StorageService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\StorageResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class StorageController extends AdminController implements HasMiddleware
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        parent::__construct();
        $this->storageService = $storageService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }
    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return StorageResource::collection($this->storageService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(Request $request): \Illuminate\Http\Response | StorageResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        $className          = 'App\\Http\\Storages\\Requests\\' . ucfirst($request->storage_type);
        $gateway            = new $className;
        $validationRequests = $request->validate($gateway->rules());
        try {
            if($gateway->envUpdateStatus) {
                $gateway->envUpdate($validationRequests);
            }
            return new StorageResource($this->storageService->update($validationRequests));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
