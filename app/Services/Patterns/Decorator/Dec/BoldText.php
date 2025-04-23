<?php

namespace App\Services\Patterns\Decorator\Dec;

class BoldText extends TextDecorator {
    public function format(string $text): string {
        return '<b>' . parent::format($text) . '</b>';
    }
}
