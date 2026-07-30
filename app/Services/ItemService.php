<?php

namespace App\Services;

use Exception;
use App\Models\Item;
use App\Enums\Status;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use App\Models\FrontendItem;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\TranslationRequest;

class ItemService
{
    public object $item;
    protected array $itemFilter = [
        'name',
        'slug',
        'item_category_id',
        'price',
        'item_type',
        'tax_id',
        'status',
        'order',
        'description',
        'is_halal',
        'discount_type',
        'except'
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

            return Item::with('media', 'category', 'tax', 'translations')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->itemFilter)) {
                        if ($key == "except") {
                            $explodes = explode('|', $request);
                            if (count($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('id', '!=', $explode);
                                }
                            }
                        } else {
                            if ($key == "item_category_id") {
                                $query->where($key, $request);
                            } else {
                                $query->where($key, 'like', '%' . $request . '%');
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ItemRequest $request): object
    {
        try {
            DB::transaction(function () use ($request) {
                $this->item = Item::create($request->validated() + ['slug' => $this->slug($request->name)]);

                if ($request->image) {
                    $frontendImage = $request->image;
                    $this->item->clearMediaCollection('item');
                    $this->item->addMedia($request->image)->preservingOriginal()->toMediaCollection('item');
                    $frontendItem = FrontendItem::find($this->item->id);
                    if ($frontendItem) {
                        $frontendItem->clearMediaCollection('frontend-item');
                        $frontendItem->addMedia($frontendImage)->toMediaCollection('frontend-item');
                    }
                }
            });
            return $this->item;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function slug(string $name): string
    {
        $slug         = Str::slug($name . rand(1000, 9999));
        $existingItem = Item::where('slug', $slug)->first();
        if ($existingItem) {
            $this->slug($name);
        }
        return $slug;
    }


    /**
     * @throws Exception
     */
    public function update(ItemRequest $request, Item $item): Item
    {
        try {
            DB::transaction(function () use ($request, $item) {
                $validated = $request->validated();
                if ($request->name !== $item->name) {
                    $validated += ['slug' => $this->slug($request->name)];
                }
                $item->update($validated);

                if ($request->image) {
                    $frontendImage = $request->image;
                    $item->clearMediaCollection('item');
                    $item->addMedia($request->image)->preservingOriginal()->toMediaCollection('item');
                    $frontendItem = FrontendItem::find($item->id);
                    if ($frontendItem) {
                        $frontendItem->clearMediaCollection('frontend-item');
                        $frontendItem->addMedia($frontendImage)->toMediaCollection('frontend-item');
                    }
                }
            });
            return $item;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Item $item): void
    {
        try {
            DB::transaction(function () use ($item) {
                $item->variations()->delete();
                $item->extras()->delete();
                $item->addons()->delete();
                $item->delete();
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
    public function show(Item $item): Item
    {
        try {
            return $item->load('translations');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTranslations(TranslationRequest $request, Item $item): \Illuminate\Http\Response
    {
        try {
            foreach ($request->get('translations', []) as $locale => $keys) {
                foreach ($keys as $key => $value) {
                    $item->translations()->updateOrCreate(
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
    public function changeImage(ChangeImageRequest $request, Item $item): Item
    {
        try {
            if ($request->image) {
                $frontendImage = $request->image;
                $item->clearMediaCollection('item');
                $item->addMedia($request->image)->preservingOriginal()->toMediaCollection('item');
                $frontendItem = FrontendItem::find($item->id);
                if ($frontendItem) {
                    $frontendItem->clearMediaCollection('frontend-item');
                    $frontendItem->addMedia($frontendImage)->toMediaCollection('frontend-item');
                }
            }
            return $item;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function itemReport(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            return Item::withCount('orders')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate('created_at', '<=', $last_date);
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->itemFilter)) {
                        if ($key == "except") {
                            $explodes = explode('|', $request);
                            if (count($explodes)) {
                                foreach ($explodes as $explode) {
                                    $query->where('id', '!=', $explode);
                                }
                            }
                        }else if ($key == "item_category_id") {
                            $query->where($key, '=',  $request);
                        }else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }
                }
            })->orderBy('orders_count', 'desc')->$method(
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
    public function searchItems(Restaurant $restaurant, PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $filters     = ['name'];

            return FrontendItem::with('translations')->where(function ($query) use ($requests, $filters) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $filters)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->where(['restaurant_id' => $restaurant->id, 'status' => Status::ACTIVE])->orderBy($orderColumn, $orderType)->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function showWithRelation(FrontendItem $frontendItem): FrontendItem
    {
        try {
            return $frontendItem->load([
                'translations',
                'tax',
                'variations' => fn($query) => $query->where(['restaurant_id' => $frontendItem->restaurant_id]),
                'addons'     => fn($query) => $query->where('restaurant_id', $frontendItem->restaurant_id)->whereHas('addonItem', fn($q) => $q->where('status', Status::ACTIVE))->with(['addonItem' => fn($q) => $q->where('status', Status::ACTIVE)])
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
