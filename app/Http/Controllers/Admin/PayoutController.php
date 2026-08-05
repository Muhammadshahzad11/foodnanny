<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Payout;
use Illuminate\Http\Request;
use App\Exports\PayoutExport;
use App\Libraries\AppLibrary;
use App\Services\PayoutService;
use App\Http\Requests\PayoutRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\PayoutResource;
use App\Http\Resources\PayoutDetailsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PayoutController extends AdminController implements HasMiddleware
{
    public PayoutService $payoutService;

    public function __construct(PayoutService $payoutService)
    {
        parent::__construct();
        $this->payoutService = $payoutService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:payouts', only: ['index', 'export']),
            new Middleware('permission:payouts_create', only: ['store']),
            new Middleware('permission:payouts_delete', only: ['destroy']),
            new Middleware('permission:payouts_show', only: ['show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return PayoutResource::collection($this->payoutService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(PayoutRequest $request): \Illuminate\Http\Response|\Illuminate\Foundation\Application|PayoutResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new PayoutResource($this->payoutService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Payout $payout): \Illuminate\Http\Response | PayoutDetailsResource
    {
        try {
            return new PayoutDetailsResource($this->payoutService->show($payout));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new PayoutExport($this->payoutService, $request), 'Payout.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function amount(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|array|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ['data' => ['amount' => AppLibrary::flatAmountFormat($this->payoutService->amount($request))]];
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Payout $payout): \Illuminate\Http\Response
    {
        try {
            $this->payoutService->destroy($payout);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
