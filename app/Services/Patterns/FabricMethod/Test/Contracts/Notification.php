<?php

namespace App\Services\Patterns\FabricMethod\Test\Contracts;

interface Notification
{
    public function send(string $message): string;
}
