<?php

namespace App\Services\Patterns\FabricMethod\Test\Realization;

use App\Services\Patterns\FabricMethod\Test\Contracts\Notification;

class EmailNotification implements Notification
{

    public function send(string $message): string
    {
            return "email_notification $message";
    }
}
