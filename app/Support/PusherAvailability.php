<?php

namespace App\Support;

class PusherAvailability
{
    public static function ready(): bool
    {
        return filled(config('broadcasting.connections.pusher.key'))
            && filled(config('broadcasting.connections.pusher.secret'))
            && filled(config('broadcasting.connections.pusher.app_id'))
            && filled(config('broadcasting.connections.pusher.options.cluster'));
    }
}
