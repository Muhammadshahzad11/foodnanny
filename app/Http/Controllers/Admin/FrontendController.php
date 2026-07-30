<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\FrontendService;
use App\Http\Requests\FrontendRequest;
use App\Http\Resources\FrontendResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FrontendController extends AdminController implements HasMiddleware
{
    public FrontendService $frontendService;

    public function __construct(FrontendService $frontendService)
    {
        parent::__construct();
        $this->frontendService = $frontendService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update', 'getTranslations', 'storeTranslations'])
        ];
    }

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|FrontendResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new FrontendResource($this->frontendService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(FrontendRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|FrontendResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               return new FrontendResource($this->frontendService->update($request));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function getTranslations(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $locale = $request->get('locale', 'en');
            return response()->json(['status' => true, 'data' => $this->frontendService->listTranslations($locale)]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function storeTranslations(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (env('DEMO')) {
                return response()->json(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            $locale = $request->get('locale');
            if (!$locale) {
                return response()->json(['status' => false, 'message' => 'Locale is required'], 422);
            }
            $this->frontendService->saveTranslations($request->except('locale'), $locale);
            return response()->json(['status' => true, 'data' => $this->frontendService->listTranslations($locale)]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
