<?php

namespace App\Services\Patterns\FabricMethod\Fabrics;

use App\Services\Patterns\FabricMethod\EmailChannel;
use App\Services\Patterns\FabricMethod\NotificationFactory;


class EmailFactory extends NotificationFactory
{

    public static function make(): EmailChannel
    {
        return new EmailChannel();
    }

}
