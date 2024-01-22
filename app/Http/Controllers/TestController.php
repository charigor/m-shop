<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Test\FactoryMethod\FactoryMethod;

class TestController extends Controller
{
    public function index(): string
    {
        return FactoryMethod::getConst();
    }
}
