<?php

namespace App\Services\Patterns\FabricMethod;

use App\Services\Patterns\FabricMethod\Contracts\NotificationChannel;

class SmsChannel implements NotificationChannel
{

    public function send(string $message): string
    {
        return "sms_$message";
    }
}
