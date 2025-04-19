<?php

namespace App\Services\Patterns\FabricMethod;

use App\Services\Patterns\FabricMethod\Contracts\NotificationChannel;

class TelegramChannel implements NotificationChannel
{

    public function send(string $message): string
    {
       return "telegram_$message";
    }
}
