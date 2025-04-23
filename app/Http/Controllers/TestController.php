<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Test\FactoryMethod\FactoryMethod;
use App\Services\Patterns\FabricMethod\TelegramChannel;

class TestController extends Controller
{
    public function index(): string
    {

        $channel = new TelegramChannel();
        return FactoryMethod::getConst();
    }
}
