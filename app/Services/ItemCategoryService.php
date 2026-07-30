<?php

namespace App\Services;

use App\Enums\Status;
use App\Libraries\AppLibrary;
use App\Models\FrontendItemCategory;
use App\Models\Restaurant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ItemCategoryRequest;
use App\Http\Requests\TranslationRequest;

class ItemCategoryService
{
    protected array $itemCateFilter = [
        'name',
        'slug',
        'description',
        'status'
    ];

    protected array $exceptFilter = [
        'excepts'
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

            return ItemCategory::with('media')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->itemCateFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
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
    public function store(ItemCategoryRequest $request)
    {
        try {
            $sort = 1;
            $itemCategorySort = ItemCategory::orderBy('sort', 'desc')->first();
            if($itemCategorySort){
                $sort = $itemCategorySort->sort + 1;
            }
            $itemCategory = ItemCategory::create($request->validated() + ['slug' => Str::slug($request->name.AppLibrary::timeWithRand()), 'sort'=> $sort]);
            if ($request->image) {
                $itemCategory->addMediaFromRequest('image')->toMediaCollection('item-category');
            }
            return $itemCategory;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ItemCategoryRequest $request, ItemCategory $itemCategory): ItemCategory
    {
        try {
            $itemCategory->update($request->validated() + ['slug' => Str::slug($request->name.AppLibrary::timeWithRand())]);
            if ($request->image) {
                $itemCategory->clearMediaCollection('item-category');
                $itemCategory->addMediaFromRequest('image')->toMediaCollection('item-category');
            }
            return $itemCategory;
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
                foreach ($request->category_id as $index => $id) {
                    ItemCategory::where('id', $id)->update(['sort' => $index + 1]);
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(ItemCategory $itemCategory): void
    {
        try {
            $checkItem = $itemCategory->items->whereNull('deleted_at');
            if (!blank($checkItem)) {
                $itemCategory->clearMediaCollection('item-category');
                $itemCategory->delete();
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                $itemCategory->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(ItemCategory $itemCategory): ItemCategory
    {
        try {
            return $itemCategory->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, ItemCategory $itemCategory): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $itemCategory->translations()->updateOrCreate(
                        ['locale' => $locale, 'key' => $key],
                        ['value' => $value ?? '']
                    );
                }
            }
            return response(['message' => trans('all.message.translations_saved_successfully')], 200);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function categoryWiseItems(Restaurant $restaurant, PaginateRequest $request)
    {
        try {
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            return FrontendItemCategory::with(['translations', 'items' => fn($query) =>$query->with('translations')->where('status', Status::ACTIVE)->where(function ($query) {$query->where(function($query) {$query->where('available_time_start', '<=', date('H:i:s'))->where('available_time_end', '>=', date('H:i:s'));})->orWhere(function($query) {$query->whereNull('available_time_start')->whereNull('available_time_end');});})])->where(['restaurant_id' => $restaurant->id, 'status' => Status::ACTIVE])->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
