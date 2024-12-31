<?php

use App\Models\ChatGroup;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

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
Broadcast::channel('group.chat.{groupId}', function (User $user, $groupId) {
    $group = ChatGroup::find($groupId);

    if (!$group) {
        return false; // Group doesn't exist
    }

    // Check User is member of group
    return $group->members->contains('user_id', $user->id);
});
