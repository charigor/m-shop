<?php

namespace App\Services\Patterns\FabricMethod\Contracts;

interface NotificationChannel
{
    public function send(string $message): string;
}
