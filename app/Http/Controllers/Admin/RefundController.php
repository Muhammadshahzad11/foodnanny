<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Refund;
use App\Exports\RefundExport;
use App\Services\RefundService;
use App\Http\Requests\RefundRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\RefundResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RefundController extends AdminController implements HasMiddleware
{
    public RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        parent::__construct();
        $this->refundService = $refundService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:refunds', only: ['index', 'export']),
            new Middleware('permission:refunds_create', only: ['store']),
            new Middleware('permission:refunds_delete', only: ['destroy'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return RefundResource::collection($this->refundService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(RefundRequest $request): \Illuminate\Http\Response | RefundResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new RefundResource($this->refundService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new RefundExport($this->refundService, $request), 'Refund.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Refund $refund): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->refundService->destroy($refund);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
