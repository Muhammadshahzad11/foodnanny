<?php

namespace App\Providers;

use App\Models\Tax;
use App\Models\Item;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\TimeSlot;
use App\Models\RestaurantTable;
use App\Models\ItemAddon;
use App\Models\ItemExtra;
use App\Models\OrderItem;
use App\Models\OrderSetup;
use App\Models\ItemCategory;
use App\Models\ItemAttribute;
use App\Models\ItemVariation;
use App\Observers\TaxObserver;
use App\Observers\ItemObserver;
use App\Observers\OrderObserver;
use App\Observers\RestaurantTableObserver;
use App\Observers\CouponObserver;
use App\Observers\TimeSlotObserver;
use App\Observers\ItemAddonObserver;
use App\Observers\ItemExtraObserver;
use App\Observers\OrderItemObserver;
use App\Observers\OrderSetupObserver;
use App\Observers\ItemCategoryObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Console\Events\CommandFinished;
use App\Services\PwaService;
use App\Observers\ItemAttributeObserver;
use App\Observers\ItemVariationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        OrderSetup::observe(OrderSetupObserver::class);
        Tax::observe(TaxObserver::class);
        RestaurantTable::observe(RestaurantTableObserver::class);
        Coupon::observe(CouponObserver::class);
        ItemCategory::observe(ItemCategoryObserver::class);
        TimeSlot::observe(TimeSlotObserver::class);
        Item::observe(ItemObserver::class);
        ItemVariation::observe(ItemVariationObserver::class);
        ItemExtra::observe(ItemExtraObserver::class);
        ItemAddon::observe(ItemAddonObserver::class);
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);
        ItemAttribute::observe(ItemAttributeObserver::class);

        RateLimiter::for('login-email', function (Request $request) {
            $key = strtolower((string)$request->input('email', '')) ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.login'))
                ->by($key . '|' . $request->ip());
        });

        RateLimiter::for('login-phone', function (Request $request) {
            $key = strtolower((string)$request->input('phone', '')) ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.login'))
                ->by($key . '|' . $request->ip());
        });

        RateLimiter::for('otp-send', function (Request $request) {
            $phone = preg_replace('/\D+/', '', (string)$request->input('phone', ''));
            $key   = $phone ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.otp_send'))->by($key . '|' . $request->ip());
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            $phone = preg_replace('/\D+/', '', (string)$request->input('phone', ''));
            $key   = $phone ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.otp_verify'))->by($key . '|' . $request->ip());
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            $key = strtolower((string)$request->input('email', '')) ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.otp_send'))->by($key . '|' . $request->ip());
        });

        RateLimiter::for('verify-code', function (Request $request) {
            $key = strtolower((string)$request->input('code', '')) ?: $request->ip();
            return Limit::perMinute(config('security.rate_limits.otp_verify'))->by($key . '|' . $request->ip());
        });

        RateLimiter::for('request-verify', function (Request $request) {
            $userId = optional($request->user())->id;
            return Limit::perMinute(config('security.rate_limits.request_verify'))->by(($userId ? "u:$userId" : "ip:" . $request->ip()));
        });

        RateLimiter::for('payment', function (Request $request) {
            $userId = optional($request->user())->id;
            return Limit::perMinute(config('security.rate_limits.payment'))->by(($userId ? "u:$userId" : "ip:" . $request->ip()));
        });

        RateLimiter::for('webhook', function (Request $request) {
            $provider = (string)$request->route('paymentGateway', 'default');
            return Limit::perMinute(config('security.rate_limits.webhook'))->by("wh:$provider|" . $request->ip());
        });

        // After optimize:clear / cache:clear, bump PWA cache so phones drop stale shells
        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if ($event->exitCode !== 0) {
                return;
            }
            if (!in_array($event->command, ['optimize:clear', 'cache:clear'], true)) {
                return;
            }
            try {
                app(PwaService::class)->forceUpdate();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('PWA bump after '.$event->command.': '.$e->getMessage());
            }
        });
    }
}
