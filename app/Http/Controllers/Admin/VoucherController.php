<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Voucher;
use App\Exports\VoucherExport;
use App\Services\VoucherService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\VoucherRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\AdminVoucherResource;
use App\Http\Resources\AdminSimpleVoucherResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class VoucherController extends AdminController implements HasMiddleware
{
    private VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        parent::__construct();
        $this->voucherService = $voucherService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:vouchers', only: ['index', 'export']),
            new Middleware('permission:vouchers_create', only: ['store']),
            new Middleware('permission:vouchers_edit', only: ['update']),
            new Middleware('permission:vouchers_delete', only: ['destroy']),
            new Middleware('permission:vouchers_show', only: ['show']),
            new Middleware('permission:vouchers_edit', only: ['saveTranslations'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return AdminSimpleVoucherResource::collection($this->voucherService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(VoucherRequest $request): AdminVoucherResource | \Illuminate\Http\Response
    {
        try {
            return new AdminVoucherResource($this->voucherService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Voucher $voucher): AdminVoucherResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new AdminVoucherResource($this->voucherService->show($voucher));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(VoucherRequest $request, Voucher $voucher): AdminVoucherResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new AdminVoucherResource($this->voucherService->update($request, $voucher));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, Voucher $voucher): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->voucherService->saveTranslations($request, $voucher);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Voucher $voucher): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->voucherService->destroy($voucher);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return Excel::download(new VoucherExport($this->voucherService, $request), 'Vouchers.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
