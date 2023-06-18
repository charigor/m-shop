<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->hasAnyRole(['manager']), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return inertia::render('Test',[
            'test' => User::all()
        ]
        );
    }
}
