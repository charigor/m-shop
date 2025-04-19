<?php

namespace App\Services\Patterns\FabricMethod\Test\Factories;

use App\Services\Patterns\FabricMethod\Test\Contracts\Notification;
use App\Services\Patterns\FabricMethod\Test\Realization\EmailNotification;

class EmailFactory extends  Factory
{

    public function make():Notification
    {
        return new EmailNotification();
    }
}
