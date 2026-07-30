<?php

namespace App\Services;

use Exception;
use App\Models\Cuisine;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CuisineRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class CuisineService
{
    protected array $cuisineFilter = ['name', 'status'];

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

            return Cuisine::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->cuisineFilter)) {
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
    public function store(CuisineRequest $request)
    {
        try {
            $sort = 1;
            $cuisineSort = Cuisine::orderBy('sort', 'desc')->first();
            if ($cuisineSort) {
                $sort = $cuisineSort->sort + 1;
            }
            $cuisine = Cuisine::create($request->validated() + ['slug' => Str::slug($request->name), 'sort' => $sort]);

            if ($request->image) {
                $cuisine->addMediaFromRequest('image')->toMediaCollection('cuisine');
            }
            return $cuisine;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CuisineRequest $request, Cuisine $cuisine): Cuisine
    {
        try {
            $cuisine->update($request->validated() + ['slug' => Str::slug($request->name)]);
            if ($request->image) {
                $cuisine->clearMediaCollection('cuisine');
                $cuisine->addMediaFromRequest('image')->toMediaCollection('cuisine');
            }
            return $cuisine;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Cuisine $cuisine): void
    {
        try {
            $cuisine->clearMediaCollection('cuisine');
            $cuisine->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Cuisine $cuisine): Cuisine
    {
        try {
            return $cuisine;
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
                foreach ($request->cuisine_id as $index => $id) {
                    Cuisine::where('id', $id)->update(['sort' => $index + 1]);
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
