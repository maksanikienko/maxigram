<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{

    public function createRoom($newUser): void
    {
        $existingUsers = User::where('id', '!=', $newUser->id)->get();

        foreach ($existingUsers as $existingUser) {
            if (!Room::where('auth_user', $newUser->id)
                ->where('friend_user', $existingUser->id)
                ->exists()) {
                Room::create([
                    'auth_user' => $newUser->id,
                    'friend_user' => $existingUser->id,
                ]);
            }
        }
    }
}
