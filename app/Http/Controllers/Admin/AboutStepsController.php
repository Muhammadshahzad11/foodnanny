<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\AboutStep;
use Illuminate\Http\Request;
use App\Services\AboutStepsService;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\AboutStepsRequest;
use App\Http\Resources\AboutStepsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class AboutStepsController extends AdminController implements HasMiddleware
{
    private AboutStepsService $aboutStepsService;

    public function __construct(AboutStepsService $aboutStepsService)
    {
        parent::__construct();
        $this->aboutStepsService = $aboutStepsService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'store', 'update', 'destroy', 'show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AboutStepsResource::collection($this->aboutStepsService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(AboutStepsRequest $request): \Illuminate\Http\Response | AboutStepsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AboutStepsResource($this->aboutStepsService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(AboutStep $aboutStep): \Illuminate\Http\Response | AboutStepsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AboutStepsResource($this->aboutStepsService->show($aboutStep));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(AboutStepsRequest $request, AboutStep $aboutStep): \Illuminate\Http\Response | AboutStepsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
             if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
              return new AboutStepsResource($this->aboutStepsService->update($request, $aboutStep));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(AboutStep $aboutStep): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                $this->aboutStepsService->destroy($aboutStep);
                return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sort(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->aboutStepsService->sort($request);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
