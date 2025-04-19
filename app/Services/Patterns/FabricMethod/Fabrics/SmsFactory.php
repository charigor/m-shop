<?php

namespace App\Services\Patterns\FabricMethod\Fabrics;

use App\Services\Patterns\FabricMethod\NotificationFactory;
use App\Services\Patterns\FabricMethod\SmsChannel;

class SmsFactory extends NotificationFactory
{

    public  static function make(): SmsChannel
    {
        return new SmsChannel();
    }
}
