<?php

namespace App\Services;

use Exception;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use App\Libraries\AppLibrary;
use App\Models\RestaurantCuisine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\MyRestaurantRequest;
use App\Http\Requests\CurrentStatusRequest;

class MyRestaurantService
{
    use DefaultAccessModelTrait;

    /**
     * @throws Exception
     */
    public function list()
    {
        try {
            return Restaurant::find($this->restaurant());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(MyRestaurantRequest $request)
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if ($restaurant) {
                DB::transaction(function () use ($request, $restaurant) {
                    tap($restaurant)->update($request->validated() + ['slug' => Str::slug($request->name.AppLibrary::timeWithRand())]);
                    if ($request->cuisine_id) {
                        $restaurant->cuisines()->delete();
                        foreach ($request->cuisine_id as $cuisine) {
                            RestaurantCuisine::create([
                                'restaurant_id' => $restaurant->id,
                                'cuisine_id'    => $cuisine
                            ]);
                        }
                    }

                    if (!$request->cuisine_id) {
                        $restaurant->cuisines()->delete();
                    }
                });
            }
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeImage(ChangeImageRequest $request)
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if ($restaurant) {
                if ($request->image) {
                    $restaurant->clearMediaCollection('restaurant');
                    $restaurant->addMedia($request->image)->toMediaCollection('restaurant');
                }
            }
            return $this->list();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeLogo(ChangeImageRequest $request)
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if ($restaurant) {
                if ($request->image) {
                    $restaurant->clearMediaCollection('restaurant-logo');
                    $restaurant->addMedia($request->image)->toMediaCollection('restaurant-logo');
                }
            }
            return $restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function currentStatus(CurrentStatusRequest $currentStatusRequest)
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if ($restaurant) {
                $restaurant->current_status = $currentStatusRequest->current_status;
                $restaurant->save();
            }
            return $restaurant;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
