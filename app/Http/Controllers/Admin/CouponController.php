<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Coupon;
use App\Exports\CouponExport;
use App\Services\CouponService;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\TranslationRequest;
use App\Http\Resources\AdminCouponResource;
use App\Http\Resources\AdminSimpleCouponResource;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CouponController extends AdminController implements HasMiddleware
{
    private CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        parent::__construct();
        $this->couponService = $couponService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:coupons', only: ['index', 'export']),
            new Middleware('permission:coupons_create', only: ['store']),
            new Middleware('permission:coupons_edit', only: ['update']),
            new Middleware('permission:coupons_delete', only: ['destroy']),
            new Middleware('permission:coupons_show', only: ['show']),
            new Middleware('permission:coupons_edit', only: ['saveTranslations'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return AdminSimpleCouponResource::collection($this->couponService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CouponRequest $request): AdminCouponResource | \Illuminate\Http\Response
    {
        try {
            return new AdminCouponResource($this->couponService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Coupon $coupon): AdminCouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdminCouponResource($this->couponService->show($coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(CouponRequest $request,Coupon $coupon): AdminCouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AdminCouponResource($this->couponService->update($request, $coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function saveTranslations(TranslationRequest $request, Coupon $coupon): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }
            return $this->couponService->saveTranslations($request, $coupon);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Coupon $coupon): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->couponService->destroy($coupon);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new CouponExport($this->couponService, $request), 'Coupons.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
