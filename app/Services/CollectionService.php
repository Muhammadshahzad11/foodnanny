<?php

namespace App\Services;

use App\Enums\Role;
use Exception;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CollectionRequest;
use App\Libraries\QueryExceptionLibrary;

class CollectionService
{
    public Collection $collection;

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

            return Collection::with('user')->where(function ($query) use ($requests) {
                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = Date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = Date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('date', '>=', $first_date)->whereDate('date', '<=', $last_date);
                }
                if (!empty($requests['amount'])) {
                    $query->where('amount', 'like', '%' . $requests['amount'] . '%');
                }

                if (!empty($requests['user_id'])) {
                    $query->where(['source_user_id' => $requests['user_id']])->orWhere(['destination_user_id' => $requests['user_id']]);
                }

                if(Auth::user()->myRole != Role::ADMIN) {
                    $query->where(['source_user_id' => Auth::user()->id])->orWhere(['destination_user_id' => Auth::user()->id]);
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
    public function store(CollectionRequest $request): Collection
    {
        try {
            DB::transaction(function () use ($request) {
                $this->collection = Collection::create([
                    'source_user_id'      => $request->source_user_id,
                    'destination_user_id' => Auth::user()->id,
                    'amount'              => $request->amount,
                    'date'                => date('Y-m-d H:i:s', strtotime($request->date)),
                ]);

                $sourceUser      = User::find($request->source_user_id);
                $destinationUser = User::find(Auth::user()->id);

                if ($sourceUser) {
                    $sourceUser->collection -= $request->amount;
                    $sourceUser->save();
                }

                if ($destinationUser) {
                    $destinationUser->collection += $request->amount;
                    $destinationUser->save();
                }
            });
            return $this->collection;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Collection $collection): void
    {
        try {
            DB::transaction(function () use ($collection) {
                $sourceUser      = User::find($collection->source_user_id);
                $destinationUser = User::find($collection->destination_user_id);

                if($sourceUser) {
                    $sourceUser->collection += $collection->amount;
                    $sourceUser->save();
                }

                if($destinationUser) {
                    $destinationUser->collection -= $collection->amount;
                    $destinationUser->save();
                }
                $collection->delete();
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
