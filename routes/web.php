<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', [DashboardController::class, 'index'])->name('index');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//    Route::get('/chat/{friend}', [ChatController::class, 'show'])->name('chat');
//    Route::get('/messages/{friend}', [ChatController::class, 'getMessages'])->name('get-messages');
//    Route::post('/messages/{friend}', [ChatController::class, 'sendMessages'])->name('send-messages');

    Route::get('/chat/rooms', [ChatController::class, 'getRooms']);
    Route::get('/chat/room/{room}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/room/{room}/send', [ChatController::class, 'sendMessages']);

});

require __DIR__.'/auth.php';
