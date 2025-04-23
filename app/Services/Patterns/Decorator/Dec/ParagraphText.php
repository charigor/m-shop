<?php

namespace App\Services\Patterns\Decorator\Dec;

class ParagraphText extends TextDecorator {
    public function format(string $text): string {
        return '<p>' . parent::format($text) . '</p>';
    }
}
