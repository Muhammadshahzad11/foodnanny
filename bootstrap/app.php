<?php


use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\EnsureRestaurantModule;
use App\Http\Middleware\Installed;
use App\Http\Middleware\Localization;
use App\Http\Middleware\VerifyEmail;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Cache\CacheManager;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->append([]);
        $middleware->validateCsrfTokens(
            except: [
                '/payment/sslcommerz/*',
                '/payment/paytm/*',
                '/payment/cashfree/*',
                '/payment/phonepe/*'
            ]
        );
        $middleware->alias([
            'auth'         => Authenticate::class,
            'permission'   => PermissionMiddleware::class,
            'role'         => RoleMiddleware::class,
            'cache'        => CacheManager::class,
            'localization' => Localization::class,
            'installed'    => Installed::class,
            'verify.api'   => VerifyEmail::class,
            'apiKey'             => ApiKeyMiddleware::class,
            'restaurant.module'  => EnsureRestaurantModule::class,
        ]);
        $middleware->web(append: [
            ThrottleRequests::class . ':120,1',
        ]);
        $middleware->api(append: [
            ThrottleRequests::class . ':120,1',
        ]);
    })
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:sanctum']],
    )
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                if ($e instanceof UnauthorizedException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User does not have the right permissions.'
                    ], 403);
                }

                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No query results for model.'
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Method not supported for the route.'
                    ], 405);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The specified URL cannot be found.'
                    ], 404);
                }

                if ($e instanceof HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'HTTP error.'
                    ], $e->getStatusCode());
                }

                if ($e instanceof QueryException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A database error occurred.',
                        'error'   => config('app.debug') ? $e->getMessage() : null
                    ], 422);
                }
            }
        });
    })->create();
