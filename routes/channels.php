<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{userId}.{otherUserId}', function ($user, $userId, $otherUserId) {

    return (int) $user->id === (int) $userId || (int) $user->id === (int) $otherUserId;
});
