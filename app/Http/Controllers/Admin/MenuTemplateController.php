<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\MenuTemplateService;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\MenuTemplateResource;

class MenuTemplateController extends AdminController
{
    private MenuTemplateService $menuTemplateService;

    public function __construct(MenuTemplateService $menuTemplate)
    {
        parent::__construct();
        $this->menuTemplateService = $menuTemplate;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return MenuTemplateResource::collection($this->menuTemplateService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
