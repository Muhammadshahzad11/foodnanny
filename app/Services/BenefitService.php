<?php

namespace App\Services;

use Exception;
use App\Models\Benefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\BenefitRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class BenefitService
{
    protected array $benefitFilter = [
        'title',
        'description',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Benefit::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->benefitFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(BenefitRequest $request)
    {
        try {
            $sort = 1;
            $benefitSort = Benefit::orderBy('sort', 'desc')->first();
            if($benefitSort){
                $sort = $benefitSort->sort + 1;
            }
            $benefit = Benefit::create($request->validated() + ['sort' => $sort]);
            if ($request->image) {
                $benefit->addMediaFromRequest('image')->toMediaCollection('benefit');
            }
            return $benefit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(BenefitRequest $request, Benefit $benefit): Benefit
    {
        try {
            $benefit->update($request->validated());
            if ($request->image) {
                $benefit->clearMediaCollection('benefit');
                $benefit->addMediaFromRequest('image')->toMediaCollection('benefit');
            }
            return $benefit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Benefit $benefit): void
    {
        try {
            $benefit->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Benefit $Benefit): Benefit
    {
        try {
            return $Benefit;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function sort(Request $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->benefit_id as $index => $id) {
                    Benefit::where('id', $id)->update(['sort' => $index + 1]);
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
