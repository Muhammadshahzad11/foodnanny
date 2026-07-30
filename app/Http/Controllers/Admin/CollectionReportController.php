<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Exports\CollectionExport;
use App\Services\CollectionService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CollectionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CollectionReportController extends AdminController implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        parent::__construct();
        $this->collectionService = $collectionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:collection-report', only: ['index', 'export'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CollectionResource::collection($this->collectionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CollectionExport($this->collectionService, $request), 'Collection-Report.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
