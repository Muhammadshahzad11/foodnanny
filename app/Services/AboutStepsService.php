<?php

namespace App\Services;

use Exception;
use App\Models\AboutStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\AboutStepsRequest;
use App\Libraries\QueryExceptionLibrary;

class AboutStepsService
{
    protected array $aboutStepsFilter = [
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

            return AboutStep::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->aboutStepsFilter)) {
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
    public function store(AboutStepsRequest $request)
    {
        try {
            $sort = 1;
            $aboutStepSort = AboutStep::orderBy('sort', 'desc')->first();
            if ($aboutStepSort) {
                $sort = $aboutStepSort->sort + 1;
            }
            $aboutStep = AboutStep::create($request->validated() + ['sort' => $sort]);
            if ($request->image) {
                $aboutStep->addMediaFromRequest('image')->toMediaCollection('about-steps');
            }
            return $aboutStep;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(AboutStepsRequest $request, AboutStep $aboutStep): AboutStep
    {
        try {
            $aboutStep->update($request->validated());
            if ($request->image) {
                $aboutStep->clearMediaCollection('about-steps');
                $aboutStep->addMediaFromRequest('image')->toMediaCollection('about-steps');
            }
            return $aboutStep;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(AboutStep $aboutStep): AboutStep
    {
        try {
            return $aboutStep;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(AboutStep $aboutStep): void
    {
        try {
            $aboutStep->clearMediaCollection('about-steps');
            $aboutStep->delete();
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
                foreach ($request->about_step_id as $index => $id) {
                    AboutStep::where('id', $id)->update(['sort' => $index + 1]);
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
