<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{orderId}.{channelType}.{userId}', function ($user, $orderId, $channelType, $userId) {
    return (bool) $user->id == $userId;
});
