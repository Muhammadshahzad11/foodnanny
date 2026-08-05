<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use App\Services\AutoLocalizationService;
use App\Http\Resources\AutoLocalizationResource;
use Exception;

class AutoLocalizationController extends Controller
{
    private AutoLocalizationService $autoLocalizationService;

    public function __construct(AutoLocalizationService $autoLocalizationService)
    {
        $this->autoLocalizationService = $autoLocalizationService;
    }

    public function index(): AutoLocalizationResource|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AutoLocalizationResource($this->autoLocalizationService->lang());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
