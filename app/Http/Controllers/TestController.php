<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Test\FactoryMethod\FactoryMethod;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * @return string
     */
    public function index() :string
    {
        return FactoryMethod::getConst();
    }
}
