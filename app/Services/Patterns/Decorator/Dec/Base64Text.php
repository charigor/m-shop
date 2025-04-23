<?php

namespace App\Services\Patterns\Decorator\Dec;

class Base64Text extends TextDecorator {
    public function format(string $text): string {
        return base64_encode(parent::format($text));
    }
}
