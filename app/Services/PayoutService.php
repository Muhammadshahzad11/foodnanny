<?php

namespace App\Services;

use Exception;
use App\Enums\Role;
use App\Models\User;
use App\Models\Payout;
use App\Enums\ModelType;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PayoutRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;

class PayoutService
{

    use DefaultAccessModelTrait;

    public Payout $payout;
    public float|int $amount;
    public StatementCalculationService $statementCalculationService;

    public function __construct(StatementCalculationService $statementCalculationService)
    {
        $this->statementCalculationService = $statementCalculationService;
    }

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

            return Payout::with('model')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
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

                if (!empty($requests['amount'])) {
                    $query->where('amount', 'like', '%' . $requests['amount'] . '%');
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
    public function store(PayoutRequest $request): Payout
    {
        try {
            DB::transaction(function () use ($request) {
                $this->payout = Payout::create([
                    'model_type' => $request->type === ModelType::RESTAURANT ? Restaurant::class : User::class,
                    'model_id'   => $request->model_id,
                    'amount'     => $request->amount,
                    'date'       => $request->date
                ]);

                if($request->type === ModelType::RESTAURANT) {
                    $this->statementCalculationService->restaurantPayout(Restaurant::find($request->model_id), $request->amount);
                } elseif($request->type === ModelType::DELIVERY_BOY) {
                    $this->statementCalculationService->deliveryBoyPayout(User::find($request->model_id), $request->amount);
                }
            });
            return $this->payout;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Payout $payout): Payout
    {
        try {
            return $payout;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function amount(Request $request)
    {
        try {
            if ($request->type == ModelType::RESTAURANT) {
                $this->amount = Restaurant::where(['id' => $request->restaurant_id])->value('balance');
            } else {
                $this->amount = User::where(['id' => $request->delivery_boy_id])->value('balance');
            }
            return $this->amount;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Payout $payout): void
    {
        try {
            $this->statementCalculationService->reversePayout($payout);
            $payout->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
