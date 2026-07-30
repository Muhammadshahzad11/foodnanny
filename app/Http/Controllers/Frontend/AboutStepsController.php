<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\AboutStepsResource;
use App\Services\AboutStepsService;

class AboutStepsController extends Controller
{
    private AboutStepsService $aboutStepsService;

    public function __construct(AboutStepsService $aboutStepsService)
    {
        $this->aboutStepsService = $aboutStepsService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AboutStepsResource::collection($this->aboutStepsService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
