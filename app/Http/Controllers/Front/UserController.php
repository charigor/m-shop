<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function getUniqueUserNames(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $uniqueUserNames = User::distinct()->pluck('name');

        return view('front.user', compact('uniqueUserNames'));
    }
}
