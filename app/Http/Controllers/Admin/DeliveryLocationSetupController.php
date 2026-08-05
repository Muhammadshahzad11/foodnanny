<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DeliveryLocationSetupRequest;
use App\Http\Resources\DeliveryLocationSetupResource;
use App\Services\DeliveryLocationSetupService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DeliveryLocationSetupController extends AdminController implements HasMiddleware
{
    public DeliveryLocationSetupService $deliveryLocationSetupService;

    public function __construct(DeliveryLocationSetupService $deliveryLocationSetupService)
    {
        parent::__construct();
        $this->deliveryLocationSetupService = $deliveryLocationSetupService;
    }
    public static function middleware(): array
    {
        return [
            new Middleware('permission:delivery-boy-settings', only: ['index', 'store'])
        ];
    }
    public function index() 
    {
        try {
            $location = $this->deliveryLocationSetupService->list();
            if($location != null) {
                return new DeliveryLocationSetupResource($location);
            }
            return null;
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(DeliveryLocationSetupRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|DeliveryLocationSetupResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new DeliveryLocationSetupResource($this->deliveryLocationSetupService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
