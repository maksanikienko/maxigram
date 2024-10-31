<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id', auth()->id())->get();
        return Inertia::render('Dashboard',compact('users'));
    }
}
