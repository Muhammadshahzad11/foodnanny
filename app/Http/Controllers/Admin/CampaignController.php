<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Campaign;
use App\Exports\CampaignExport;
use App\Services\CampaignService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CampaignRequest;
use App\Http\Resources\AdminCampaignResource;
use App\Http\Resources\AdminSimpleCampaignResource;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\TranslationRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CampaignController extends AdminController implements HasMiddleware
{
    private CampaignService $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        parent::__construct();
        $this->campaignService = $campaignService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:campaigns', only: ['index', 'export', 'changeImage']),
            new Middleware('permission:campaigns_create', only: ['store']),
            new Middleware('permission:campaigns_edit', only: ['update', 'saveTranslations']),
            new Middleware('permission:campaigns_show', only: ['show']),
            new Middleware('permission:campaigns_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AdminSimpleCampaignResource::collection($this->campaignService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CampaignRequest $request)
    {
        try {
            return new AdminCampaignResource($this->campaignService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Campaign $campaign): \Illuminate\Http\Response|AdminCampaignResource
    {
        try {
            return new AdminCampaignResource($this->campaignService->show($campaign));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CampaignRequest $request, Campaign $campaign): \Illuminate\Http\Response|AdminCampaignResource
    {
        try {
            return new AdminCampaignResource($this->campaignService->update($request, $campaign));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, Campaign $campaign): \Illuminate\Http\Response
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->campaignService->saveTranslations($request, $campaign);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Campaign $campaign): \Illuminate\Http\Response
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
              $this->campaignService->destroy($campaign);
            return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CampaignExport($this->campaignService, $request), 'Campaigns.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeThumbnail(ChangeImageRequest $request, Campaign $campaign): \Illuminate\Http\Response|AdminCampaignResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                return new AdminCampaignResource($this->campaignService->changeThumbnail($request, $campaign));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeCover(ChangeImageRequest $request, Campaign $campaign): \Illuminate\Http\Response|AdminCampaignResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                return new AdminCampaignResource($this->campaignService->changeCover($request, $campaign));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
