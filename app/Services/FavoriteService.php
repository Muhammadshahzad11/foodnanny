<?php

namespace App\Services;


use Exception;
use App\Models\Favorite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\FavoriteRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class FavoriteService
{
    protected array $favoriteFilter = [
        'restaurant_id'
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

            if (Auth::check()) {
                return Favorite::where(function ($query) use ($requests) {
                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->favoriteFilter)) {
                            $query->where($key, $request);
                        }
                    }
                })->where(['user_id' => Auth::user()->id])->orderBy($orderColumn, $orderType)->$method($methodValue);
            } else {
                return collect([]);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function toggle(FavoriteRequest $request): void
    {
        try {
            $favorite = Favorite::where(['restaurant_id' => $request->restaurant_id, 'user_id' => Auth::user()->id])->first();
            if ($request->toggle) {
                if (!$favorite) {
                    Favorite::create([
                        'restaurant_id' => $request->restaurant_id,
                        'user_id'       => Auth::user()->id
                    ]);
                }
            } else {
                if ($favorite) {
                    $favorite->delete();
                }
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
