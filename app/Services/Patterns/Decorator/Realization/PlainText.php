<?php

namespace App\Services\Patterns\Decorator\Realization;

use App\Services\Patterns\Decorator\Contracts\Formatter;

class PlainText implements Formatter {
    public function format(string $text): string {
        return $text;
    }
}
