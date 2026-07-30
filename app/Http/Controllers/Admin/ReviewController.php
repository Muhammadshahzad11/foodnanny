<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Review;
use App\Exports\ReviewExport;
use App\Services\ReviewService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ReviewController extends AdminController implements HasMiddleware
{
    public mixed $review;
    public ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        parent::__construct();
        $this->reviewService = $reviewService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:reviews|restaurants|delivery-boys', only: ['index']),
            new Middleware('permission:reviews', only: ['export']),
            new Middleware('permission:reviews_create', only: ['store']),
            new Middleware('permission:reviews_edit', only: ['update']),
            new Middleware('permission:reviews_delete', only: ['destroy']),
            new Middleware('permission:reviews_show|restaurants|delivery-boys', only: ['show'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ReviewResource::collection($this->reviewService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ReviewRequest $request): \Illuminate\Http\Response | ReviewResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReviewResource($this->reviewService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ReviewRequest $request, Review $review): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReviewResource($this->reviewService->update($request, $review));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Review $review): \Illuminate\Foundation\Application|\Illuminate\Http\Response|ReviewResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ReviewResource($this->reviewService->show($review));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ReviewExport($this->reviewService, $request), 'Review.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Review $review): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->reviewService->destroy($review);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
