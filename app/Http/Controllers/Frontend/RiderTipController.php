<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\SimpleRiderTipResource;
use App\Services\RiderTipService;
use Exception;

class RiderTipController extends Controller
{
    private RiderTipService $riderTipService;

    public function __construct(RiderTipService $riderTipService)
    {
        $this->riderTipService = $riderTipService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleRiderTipResource::collection($this->riderTipService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
