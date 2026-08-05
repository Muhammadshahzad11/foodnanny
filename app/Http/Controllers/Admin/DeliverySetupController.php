<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\DeliverySetupService;
use App\Http\Requests\DeliverySetupRequest;
use App\Http\Resources\DeliverySetupResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class DeliverySetupController extends AdminController implements HasMiddleware
{
    public DeliverySetupService $deliverySetupService;

    public function __construct(DeliverySetupService $deliverySetupService)
    {
        parent::__construct();
        $this->deliverySetupService = $deliverySetupService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Http\Response | DeliverySetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DeliverySetupResource($this->deliverySetupService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(DeliverySetupRequest $request): \Illuminate\Http\Response | DeliverySetupResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new DeliverySetupResource($this->deliverySetupService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
