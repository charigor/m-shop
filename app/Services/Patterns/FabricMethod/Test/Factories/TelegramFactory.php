<?php

namespace App\Services\Patterns\FabricMethod\Test\Factories;

use App\Services\Patterns\FabricMethod\Test\Contracts\Notification;
use App\Services\Patterns\FabricMethod\Test\Realization\TelegramNotification;

class TelegramFactory extends  Factory
{

    public function make(): Notification
    {
        return new TelegramNotification();
    }
}
