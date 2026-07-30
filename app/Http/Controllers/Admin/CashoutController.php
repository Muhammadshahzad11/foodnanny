<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CashoutDetailsResource;
use Exception;
use App\Models\Cashout;
use App\Services\CashoutService;
use App\Exports\CashoutExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CashoutResource;
use App\Http\Requests\CashoutRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CashoutController extends AdminController implements HasMiddleware
{
    private CashoutService $cashoutService;

    public function __construct(CashoutService $cashoutService)
    {
        parent::__construct();
        $this->cashoutService = $cashoutService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:cashouts', only: ['index', 'export']),
            new Middleware('permission:cashouts_create', only: ['store']),
            new Middleware('permission:cashouts_delete', only: ['destroy']),
            new Middleware('permission:cashouts_show', only: ['show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return CashoutResource::collection($this->cashoutService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CashoutRequest $request): \Illuminate\Http\Response|CashoutResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CashoutResource($this->cashoutService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Cashout $cashout): CashoutDetailsResource|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new CashoutDetailsResource($this->cashoutService->show($cashout));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Cashout $cashout): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->cashoutService->destroy($cashout);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CashoutExport($this->cashoutService, $request), 'Cashout.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
