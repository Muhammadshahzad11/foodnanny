<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\TermsAndConditionsService;
use App\Http\Requests\TermsAndConditionsRequest;
use App\Http\Resources\TermsAndConditionsResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TermsAndConditionsController extends AdminController implements HasMiddleware
{
    private TermsAndConditionsService $termsAndConditionsService;

    public function __construct(TermsAndConditionsService $termsAndConditionsService)
    {
        parent::__construct();
        $this->termsAndConditionsService = $termsAndConditionsService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:system_settings', only: ['index', 'update'])
        ];
    }

    public function index(): \Illuminate\Http\Response | TermsAndConditionsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new TermsAndConditionsResource($this->termsAndConditionsService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(TermsAndConditionsRequest $request): \Illuminate\Http\Response | TermsAndConditionsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new TermsAndConditionsResource($this->termsAndConditionsService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
