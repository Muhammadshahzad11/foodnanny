<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Ask;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CuisineResource;
use App\Services\CuisineService;
use Exception;

class CuisineController extends AdminController
{
    private CuisineService $cuisineService;

    public function __construct(CuisineService $cuisineService)
    {
        parent::__construct();
        $this->cuisineService = $cuisineService;
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            // Home / PWA / mobile slider: only cuisines marked show-on-home.
            // Admin CRUD uses a separate route and is not affected.
            if (!$request->filled('show_on_home')) {
                $request->merge(['show_on_home' => Ask::YES]);
            }

            return CuisineResource::collection($this->cuisineService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
