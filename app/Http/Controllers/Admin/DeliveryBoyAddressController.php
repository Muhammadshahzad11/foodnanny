<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Address;
use App\Services\UserAddressService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\DeliveryBoyAddressRequest;
use Illuminate\Routing\Controllers\HasMiddleware;

class DeliveryBoyAddressController extends AdminController implements HasMiddleware
{

    private UserAddressService $userAddressService;

    public function __construct(UserAddressService $userAddressService)
    {
        parent::__construct();
        $this->userAddressService = $userAddressService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:delivery-boys_show', only: ['index', 'show']),
            new Middleware('permission:delivery-boys_create', only: ['store']),
            new Middleware('permission:delivery-boys_edit', only: ['update']),
            new Middleware('permission:delivery-boys_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request, User $deliveryBoy): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AddressResource::collection($this->userAddressService->list($request, $deliveryBoy));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(DeliveryBoyAddressRequest $request, User $deliveryBoy): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->store($request, $deliveryBoy));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(DeliveryBoyAddressRequest $request, User $deliveryBoy, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->update($request, $deliveryBoy, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $deliveryBoy, Address $address): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->userAddressService->destroy($deliveryBoy, $address);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $deliveryBoy, Address $address): \Illuminate\Http\Response | AddressResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AddressResource($this->userAddressService->show($deliveryBoy, $address));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
