<?php

namespace App\Services;

use App\Enums\Role as EnumRole;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RestaurantTableRequest;
use App\Http\Requests\RestaurantTableStatusRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\RestaurantTable;
use App\Traits\DefaultAccessModelTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RestaurantTableService
{
    use DefaultAccessModelTrait;

    protected array $tableFilter = [
        'name',
        'table_number',
        'zone',
        'status',
        'restaurant_id',
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
            $search      = trim((string) $request->get('search', ''));

            return RestaurantTable::with(['restaurant:id,name', 'creator:id,name', 'editor:id,name'])
                ->where(function ($query) use ($requests, $search) {
                    foreach ($requests as $key => $value) {
                        if ($value === '' || $value === null || !in_array($key, $this->tableFilter, true)) {
                            continue;
                        }

                        if (in_array($key, ['status', 'restaurant_id'], true)) {
                            $query->where($key, $value);
                        } else {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
                    }

                    if ($search !== '') {
                        $query->where(function ($inner) use ($search) {
                            $inner->where('name', 'like', '%' . $search . '%')
                                ->orWhere('table_number', 'like', '%' . $search . '%')
                                ->orWhere('zone', 'like', '%' . $search . '%');
                        });
                    }
                })
                ->orderBy($orderColumn, $orderType)
                ->$method($methodValue);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(RestaurantTableRequest $request): RestaurantTable
    {
        try {
            $this->assertRestaurantWritable($request->resolveRestaurantId());

            $payload = $request->validated();
            if ($this->restaurant() > 0) {
                $payload['restaurant_id'] = $this->restaurant();
            } else {
                $payload['restaurant_id'] = $request->resolveRestaurantId();
            }

            if (empty($payload['restaurant_id'])) {
                throw new Exception(trans('all.message.restaurant_required_for_table'), 422);
            }

            return RestaurantTable::create($payload)->load(['restaurant:id,name', 'creator:id,name', 'editor:id,name']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function update(RestaurantTableRequest $request, RestaurantTable $restaurantTable): RestaurantTable
    {
        try {
            $this->assertCanManage($restaurantTable);

            $payload = $request->validated();
            if ($this->restaurant() > 0) {
                $payload['restaurant_id'] = $this->restaurant();
            } elseif (!empty($payload['restaurant_id'])) {
                $this->assertRestaurantWritable((int) $payload['restaurant_id']);
            } else {
                unset($payload['restaurant_id']);
            }

            $restaurantTable->update($payload);

            return $restaurantTable->fresh()->load(['restaurant:id,name', 'creator:id,name', 'editor:id,name']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function show(RestaurantTable $restaurantTable): RestaurantTable
    {
        try {
            $this->assertCanManage($restaurantTable);

            return $restaurantTable->load(['restaurant:id,name', 'creator:id,name', 'editor:id,name']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(RestaurantTable $restaurantTable): void
    {
        try {
            $this->assertCanManage($restaurantTable);

            if ($restaurantTable->orders()->exists()) {
                throw new Exception(trans('all.message.table_has_orders'), 422);
            }

            $restaurantTable->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    public function changeStatus(RestaurantTableStatusRequest $request, RestaurantTable $restaurantTable): RestaurantTable
    {
        try {
            $this->assertCanManage($restaurantTable);
            $restaurantTable->update(['status' => $request->validated('status')]);

            return $restaurantTable->fresh()->load(['restaurant:id,name', 'creator:id,name', 'editor:id,name']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(
                $exception->getCode() === 422 ? $exception->getMessage() : QueryExceptionLibrary::message($exception),
                422
            );
        }
    }

    /**
     * @throws Exception
     */
    protected function assertCanManage(RestaurantTable $restaurantTable): void
    {
        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && (int) $restaurantTable->restaurant_id !== $scoped) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }

        $user = Auth::user();
        if (
            $user
            && isset($user->roles[0])
            && (int) $user->roles[0]->id === EnumRole::RESTAURANT_OWNER
            && (int) $user->restaurant_id > 0
            && (int) $restaurantTable->restaurant_id !== (int) $user->restaurant_id
        ) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertRestaurantWritable(int $restaurantId): void
    {
        if ($restaurantId <= 0) {
            throw new Exception(trans('all.message.restaurant_required_for_table'), 422);
        }

        $scoped = (int) $this->restaurant();
        if ($scoped > 0 && $scoped !== $restaurantId) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }

        $user = Auth::user();
        if (
            $user
            && isset($user->roles[0])
            && (int) $user->roles[0]->id === EnumRole::RESTAURANT_OWNER
            && (int) $user->restaurant_id > 0
            && (int) $user->restaurant_id !== $restaurantId
        ) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }
}
