<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Offer;
use App\Exports\OfferExport;
use App\Services\OfferService;
use App\Http\Requests\OfferRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\AdminOfferResource;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TranslationRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Resources\AdminSimpleOfferResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OfferController extends AdminController implements HasMiddleware
{
    private OfferService $offerService;

    public function __construct(OfferService $offer)
    {
        parent::__construct();
        $this->offerService = $offer;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:offers', only: ['index', 'store', 'show', 'update', 'export', 'changeImage']),
            new Middleware('permission:offers_create', only: ['store']),
            new Middleware('permission:offers_edit', only: ['update']),
            new Middleware('permission:offers_delete', only: ['destroy']),
            new Middleware('permission:offers_show', only: ['show']),
            new Middleware('permission:offers_edit', only: ['saveTranslations'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return AdminSimpleOfferResource::collection($this->offerService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OfferRequest $request): \Illuminate\Http\Response | AdminOfferResource
    {
        try {
            return new AdminOfferResource($this->offerService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Offer $offer): \Illuminate\Http\Response | AdminOfferResource
    {
        try {
            return new AdminOfferResource($this->offerService->show($offer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(OfferRequest $request, Offer $offer): \Illuminate\Http\Response | AdminOfferResource
    {
        try {
            return new AdminOfferResource($this->offerService->update($request, $offer));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, Offer $offer): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->offerService->saveTranslations($request, $offer);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Offer $offer): \Illuminate\Http\Response
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            } else {
                $this->offerService->destroy($offer);
                return response('', 202);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return Excel::download(new OfferExport($this->offerService, $request), 'Offers.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeThumbnail(ChangeImageRequest $request, Offer $offer): \Illuminate\Http\Response | AdminOfferResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               return new AdminOfferResource($this->offerService->changeThumbnail($request, $offer));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeCover(ChangeImageRequest $request, Offer $offer): \Illuminate\Http\Response | AdminOfferResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
             if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
                 return new AdminOfferResource($this->offerService->changeCover($request, $offer));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
