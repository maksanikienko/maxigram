<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('get-rooms');
    }
    return view('landing');
})->name('index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat/rooms', [ChatController::class, 'getRooms'])->name('get-rooms');
    Route::get('/chat/room/{room}/messages', [ChatController::class, 'getMessages'])->name('get-messages');
    Route::post('/chat/room/{room}/send', [ChatController::class, 'sendMessages'])->name('send-messages');
    Route::patch('/chat/message/{message}', [ChatController::class, 'updateMessage'])->name('update-message');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('delete-message');

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push/subscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
