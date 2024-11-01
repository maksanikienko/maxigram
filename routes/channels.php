<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;


//Broadcast::channel('chat.{id}', function ($user, $id) {
//
//    return (int) $user->id === (int) $id;
//});
// Channel auth for each room
Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {

    $room = Room::where('id', $roomId)
        ->where(function($query) use ($user) {
            $query->where('auth_user', $user->id)
                ->orWhere('friend_user', $user->id);
        })->exists();
    return $room ? ['id' => $user->id, 'name' => $user->name] : false;
});

Broadcast::channel('presence-chat.main', function (User $user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'is_online' => true,
    ];
});
