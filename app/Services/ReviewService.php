<?php

namespace App\Services;


use Exception;
use App\Enums\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Enums\ModelType;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\RestaurantRatingRequest;
use App\Http\Requests\DeliveryBoyRatingRequest;

class ReviewService
{
    use DefaultAccessModelTrait;
    public mixed $review;

    protected array $reviewFilter = [
        'star',
        'review',
        'model_type'
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

            return Review::with('user', 'model')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->reviewFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate('created_at', '<=', $last_date);
                }

                if ($this->restaurant()) {
                    $query->where(['model_type' => Restaurant::class, 'model_id' => $this->restaurant()]);
                } elseif (Auth::user()->my_role == Role::DELIVERY_BOY) {
                    $query->where(['model_type' => User::class, 'model_id' => Auth::user()->id]);
                } else {
                    if ((isset($requests['type']) && isset($requests['restaurant_id'])) && $requests['type'] == ModelType::RESTAURANT && $requests['restaurant_id']) {
                        $query->where(['model_type' => Restaurant::class, 'model_id' => $requests['restaurant_id']]);
                    } elseif ((isset($requests['type']) && isset($requests['delivery_boy_id'])) && $requests['type'] == ModelType::DELIVERY_BOY && $requests['delivery_boy_id']) {
                        $query->where(['model_type' => User::class, 'model_id' => $requests['delivery_boy_id']]);
                    } elseif (isset($requests['type']) && $requests['type'] == ModelType::RESTAURANT) {
                        $query->where(['model_type' => Restaurant::class]);
                    } elseif (isset($requests['type']) && $requests['type'] == ModelType::DELIVERY_BOY) {
                        $query->where(['model_type' => User::class]);
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
    public function store(ReviewRequest $request): object
    {
        try {
            $order = Order::where(['order_serial_no' => $request->order_id])->firstOrFail();
            DB::transaction(function () use ($request, $order) {
                $this->review = Review::create([
                    'user_id'    => $order->user_id,
                    'model_type' => $request->type == ModelType::RESTAURANT ? Restaurant::class : User::class,
                    'model_id'   => $request->type == ModelType::RESTAURANT ? $order->restaurant_id : $order->delivery_boy_id,
                    'star'       => $request->star,
                    'review'     => $request->review,
                    'created_at' => now(),
                ]);
            });
            return $this->review;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ReviewRequest $request, Review $review): Review
    {
        try {
            DB::transaction(function () use ($request,$review) {
                $review->update([
                    'star'       => $request->star,
                    'review'     => $request->review,
                    'updated_at' => now()
                ]);
            });
            return $review;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Review $review): Review
    {
        try {
            return $review;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Review $review): void
    {
        try {
            $review->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function frontendRestaurantReviewStore(Order $order, RestaurantRatingRequest $request)
    {
        try {
            $review = Review::where(['user_id' => Auth::user()->id, 'model_type' => Restaurant::class, 'model_id' => $order->restaurant_id])->first();
            if ($review) {
                $review->star   = $request->star;
                $review->review = $request->review;
                $review->save();
            } else {
                $review = Review::create([
                    'user_id'    => Auth::user()->id,
                    'model_type' => Restaurant::class,
                    'model_id'   => $order->restaurant_id,
                    'star'       => $request->star,
                    'review'     => $request->review
                ]);
            }
            return $review;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function frontendRestaurantReviewShow(Order $order)
    {
        try {
            return Review::where(['model_type' => Restaurant::class, 'model_id' => $order->restaurant_id, 'user_id' => Auth::user()->id])->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function frontendDeliveryBoyReviewStore(Order $order, DeliveryBoyRatingRequest $request)
    {
        try {
            $review = Review::where(['user_id' => Auth::user()->id, 'model_type' => User::class, 'model_id' => $order->delivery_boy_id])->first();
            if ($review) {
                $review->star   = $request->star;
                $review->review = $request->review;
                $review->save();
            } else {
                $review = Review::create([
                    'user_id'    => Auth::user()->id,
                    'model_type' => User::class,
                    'model_id'   => $order->delivery_boy_id,
                    'star'       => $request->star,
                    'review'     => $request->review
                ]);
            }
            return $review;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function frontendDeliveryBoyReviewShow(Order $order)
    {
        try {
            return Review::where(['model_type' => User::class, 'model_id' => $order->delivery_boy_id, 'user_id' => Auth::user()->id])->first();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
