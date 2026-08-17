<?php

namespace App\Http\Middleware;

use App\Services\RestaurantModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRestaurantModule
{
    public function __construct(protected RestaurantModuleService $restaurantModuleService)
    {
    }

    public function handle(Request $request, Closure $next, string $module): Response
    {
        $this->restaurantModuleService->assertEnabled($module);

        return $next($request);
    }
}
