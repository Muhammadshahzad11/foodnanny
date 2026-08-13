<?php

namespace App\Providers;

use App\Events\OrderPlacedSMS;
use App\Events\OtpRequested;
use App\Events\RestaurantDeliveryBoyOrderAcceptSMS;
use App\Events\RestaurantDeliveryBoyOrderPickedSMS;
use App\Events\RestaurantOrderReceivedSMS;
use App\Listeners\SendOrderConfirmationSMS;
use App\Listeners\SendOtpNotification;
use App\Listeners\SendRestaurantDeliveryBoyOrderAcceptSMS;
use App\Listeners\SendRestaurantDeliveryBoyOrderPickedSMS;
use App\Listeners\SendRestaurantOrderSMS;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OtpRequested::class => [
            SendOtpNotification::class,
        ],
        OrderPlacedSMS::class => [
            SendOrderConfirmationSMS::class,
        ],
        RestaurantOrderReceivedSMS::class => [
            SendRestaurantOrderSMS::class,
        ],
        RestaurantDeliveryBoyOrderAcceptSMS::class => [
            SendRestaurantDeliveryBoyOrderAcceptSMS::class,
        ],
        RestaurantDeliveryBoyOrderPickedSMS::class => [
            SendRestaurantDeliveryBoyOrderPickedSMS::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Discover remaining email/push listeners under app/Listeners.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
