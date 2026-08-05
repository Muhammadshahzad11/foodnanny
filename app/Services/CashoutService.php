<?php

namespace App\Services;


use App\Models\User;
use Exception;
use App\Models\Cashout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CashoutRequest;
use App\Libraries\QueryExceptionLibrary;

class CashoutService
{
    public Cashout $cashout;

    public array $cashoutFilter = ['amount', 'user_id'];

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

            return Cashout::with('user')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->cashoutFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('date', '>=', $first_date)->whereDate('date', '<=', $last_date);
                }
                if (!empty($requests['user_id'])) {
                    $query->where(['user_id' => $requests['user_id']]);
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
    public function store(CashoutRequest $request): Cashout
    {
        try {
            DB::transaction(function () use ($request) {
                $this->cashout = Cashout::create([
                    'user_id'        => $request->user_id,
                    'amount'         => $request->amount,
                    'date'           => $request->date,
                    'transaction_id' => $request->transaction_id,
                    'remarks'        => $request->remarks
                ]);
                if ($request->file) {
                    $this->cashout->addMediaFromRequest('file')->toMediaCollection('cashout');
                }

                $user = User::find($request->user_id);
                if ($user) {
                    $user->collection -= $request->amount;
                    $user->save();
                }
            });
            return $this->cashout;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Cashout $cashout): Cashout
    {
        try {
            return $cashout->load('user', 'media');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Cashout $cashout): void
    {
        try {
            DB::transaction(function () use ($cashout) {
                $user = User::find($cashout->user_id);
                if ($user) {
                    $user->collection += $cashout->amount;
                    $user->save();
                }
                $cashout->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
