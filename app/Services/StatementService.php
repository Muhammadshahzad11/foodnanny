<?php

namespace App\Services;

use Exception;
use App\Enums\Role;
use App\Models\User;
use App\Enums\ModelType;
use App\Models\Statement;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Traits\DefaultAccessModelTrait;
use App\Libraries\QueryExceptionLibrary;

class StatementService
{
    use DefaultAccessModelTrait;
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

            return Statement::with('order')->where(function ($query) use ($requests) {
                if (isset($requests['order_serial_no'])) {
                    $query->whereHas('order', function ($query) use ($requests) {
                        $query->where(['order_serial_no' => $requests['order_serial_no']]);
                    });
                }

                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('date', '>=', $first_date)->whereDate('date', '<=', $last_date);
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
}
