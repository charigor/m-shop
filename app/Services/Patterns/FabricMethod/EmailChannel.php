<?php

namespace App\Services\Patterns\FabricMethod;

use App\Services\Patterns\FabricMethod\Contracts\NotificationChannel;

class EmailChannel implements NotificationChannel
{

    public function send(string $message): string
    {
       return "email_$message";
    }
}
