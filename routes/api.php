<?php

use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->prefix('groups')
    ->group(function () {
        Route::get('/get', [GroupController::class, 'getGroups']);
        Route::post('/create', [GroupController::class, 'createGroup']);
        Route::post('/add/{groupId}/members', [GroupController::class, 'addMember']);
        Route::get('/get/{groupId}/messages', [GroupController::class, 'getGroupMessages']);
        Route::post('/send/{groupId}/messages', [GroupController::class, 'sendMessage']);
    });
