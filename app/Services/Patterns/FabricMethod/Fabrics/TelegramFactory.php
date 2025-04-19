<?php

namespace App\Services\Patterns\FabricMethod\Fabrics;


use App\Services\Patterns\FabricMethod\NotificationFactory;
use App\Services\Patterns\FabricMethod\TelegramChannel;

class TelegramFactory extends NotificationFactory
{

    public static function make(): TelegramChannel
    {
        return new TelegramChannel();
    }

}
