<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Collection;
use App\Exports\CollectionExport;
use App\Services\CollectionService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CollectionController extends AdminController implements HasMiddleware
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        parent::__construct();
        $this->collectionService = $collectionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:collections', only: ['index']),
            new Middleware('permission:collections', only: ['export']),
            new Middleware('permission:collections_create', only: ['store']),
            new Middleware('permission:collections_delete', only: ['destroy']),
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CollectionResource::collection($this->collectionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CollectionRequest $request): \Illuminate\Http\Response|CollectionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CollectionResource($this->collectionService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Collection $collection): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->collectionService->destroy($collection);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CollectionExport($this->collectionService, $request), 'Collection.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
