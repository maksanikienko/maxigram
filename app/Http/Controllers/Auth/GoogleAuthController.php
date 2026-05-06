<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RoomController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'               => $googleUser->getName(),
                'google_id'          => $googleUser->getId(),
                'image'              => $googleUser->getAvatar(),
                'email_verified_at'  => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            (new RoomController)->createRoom($user);
        }

        Auth::login($user);

        return redirect()->route('get-rooms');
    }
}
