<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Requests\KitchenStationRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\KitchenStation;
use App\Models\Printer;
use App\Traits\DefaultAccessModelTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KitchenStationService
{
    use DefaultAccessModelTrait;

    protected array $filter = [
        'name',
        'code',
        'status',
        'printer_id',
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
            $orderColumn = $request->get('order_column') ?? 'sort_order';
            $orderType   = $request->get('order_type') ?? 'asc';

            return KitchenStation::query()
                ->with(['printer', 'categories', 'restaurant'])
                ->where(function ($query) use ($requests) {
                    foreach ($requests as $key => $value) {
                        if (in_array($key, $this->filter, true)) {
                            $query->where($key, 'like', '%' . $value . '%');
                        }
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
    public function store(KitchenStationRequest $request): KitchenStation
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $this->assertPrinterBelongs($data['printer_id'] ?? null);

                $station = KitchenStation::create([
                    'restaurant_id' => $this->restaurant(),
                    'name'          => $data['name'],
                    'code'          => $data['code'] ?? Str::slug($data['name']),
                    'sort_order'    => (int) ($data['sort_order'] ?? 0),
                    'status'        => (int) ($data['status'] ?? Status::ACTIVE),
                    'printer_id'    => $data['printer_id'] ?? null,
                ]);

                $station->categories()->sync($data['category_ids'] ?? []);

                return $station->load(['printer', 'categories']);
            });
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
    public function update(KitchenStationRequest $request, KitchenStation $kitchenStation): KitchenStation
    {
        try {
            return DB::transaction(function () use ($request, $kitchenStation) {
                $data = $request->validated();
                $this->assertPrinterBelongs($data['printer_id'] ?? null);

                $kitchenStation->update([
                    'name'       => $data['name'],
                    'code'       => $data['code'] ?? $kitchenStation->code,
                    'sort_order' => (int) ($data['sort_order'] ?? $kitchenStation->sort_order),
                    'status'     => (int) ($data['status'] ?? $kitchenStation->status),
                    'printer_id' => $data['printer_id'] ?? null,
                ]);

                $kitchenStation->categories()->sync($data['category_ids'] ?? []);

                return $kitchenStation->fresh(['printer', 'categories']);
            });
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
    public function destroy(KitchenStation $kitchenStation): void
    {
        try {
            $kitchenStation->categories()->detach();
            $kitchenStation->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertPrinterBelongs(mixed $printerId): void
    {
        if (blank($printerId)) {
            return;
        }

        $exists = Printer::query()
            ->where('id', (int) $printerId)
            ->where('restaurant_id', $this->restaurant())
            ->exists();

        if (!$exists) {
            throw new Exception(trans('all.message.printer_not_found'), 422);
        }
    }
}
