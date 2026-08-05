<?php

namespace App\Exports;

use App\Libraries\AppLibrary;
use App\Services\ReviewService;
use App\Http\Requests\PaginateRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReviewExport implements FromCollection, WithHeadings
{
    public ReviewService $reviewService;
    public PaginateRequest $request;

    public function __construct(ReviewService $reviewService, $request)
    {
        $this->reviewService = $reviewService;
        $this->request       = $request;
    }

    public function collection()
    {
        $reviewArray = [];
        $reviews     = $this->reviewService->list($this->request);

        foreach ($reviews as $review) {
            $reviewArray[] = [
                $review->model?->name,
                $review->star,
                $review->review,
                AppLibrary::datetime($review->created_at)
            ];
        }
        return collect($reviewArray);
    }

    public function headings(): array
    {
        return [
            trans('all.label.name'),
            trans('all.label.rating'),
            trans('all.label.review'),
            trans('all.label.date')
        ];
    }
}
