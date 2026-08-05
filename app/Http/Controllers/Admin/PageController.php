<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Page;
use App\Services\PageService;
use App\Http\Requests\PageRequest;
use App\Http\Resources\PageResource;
use App\Http\Requests\PaginateRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class PageController extends AdminController implements HasMiddleware
{
    private PageService $pageService;

    public function __construct(PageService $page)
    {
        parent::__construct();
        $this->pageService = $page;
    }

    public static function middleware()
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'store', 'update', 'destroy', 'show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return PageResource::collection($this->pageService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(PageRequest $request): \Illuminate\Http\Response | PageResource
    {
        try {
            return new PageResource($this->pageService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Page $page): \Illuminate\Http\Response | PageResource
    {
        try {
            return new PageResource($this->pageService->show($page));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(PageRequest $request, Page $page): \Illuminate\Http\Response | PageResource
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               return new PageResource($this->pageService->update($request, $page));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Page $page): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                $this->pageService->destroy($page);
                return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
