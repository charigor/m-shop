<?php

namespace App\Services\Patterns\FabricMethod;

use App\Services\Patterns\FabricMethod\Contracts\NotificationChannel;

abstract class NotificationFactory
{
    abstract public static function make();


}
