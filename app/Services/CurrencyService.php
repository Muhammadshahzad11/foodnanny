<?php

namespace App\Services;

use Exception;
use App\Models\Currency;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CurrencyRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use Dipokhalder\Settings\Facades\Settings;

class CurrencyService
{
    protected array $currencyFilter = [
        'name',
        'symbol',
        'code',
        'is_cryptocurrency',
        'exchange_rate'
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

            return Currency::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->currencyFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
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
    public function store(CurrencyRequest $request)
    {
        try {
            return Currency::create($request->validated());
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CurrencyRequest $request, Currency $currency)
    {
        try {
            $updated = tap($currency)->update($request->validated());

            // Keep site default currency symbol/code in sync when the active currency is edited
            $defaultId = (int) Settings::group('site')->get('site_default_currency');
            if ($defaultId === (int) $currency->id) {
                Settings::group('site')->set([
                    'site_default_currency_symbol' => $currency->symbol,
                ]);

                app(SiteService::class)->syncRuntimeCurrencyEnv(
                    (string) $currency->code,
                    (string) $currency->symbol,
                    (string) Settings::group('site')->get('site_currency_position'),
                    (string) Settings::group('site')->get('site_digit_after_decimal_point')
                );
            }

            return $updated;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Currency $currency): void
    {
        try {
            if (Settings::group('site')->get("site_default_currency") != $currency->id) {
                $currency->delete();
            } else {
                throw new Exception("Default currency not deletable", 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
