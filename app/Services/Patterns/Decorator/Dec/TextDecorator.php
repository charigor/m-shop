<?php

namespace App\Services\Patterns\Decorator\Dec;

use App\Services\Patterns\Decorator\Contracts\Formatter;

abstract class TextDecorator implements Formatter {
    protected Formatter $component;

    public function __construct(Formatter $component) {
        $this->component = $component;
    }

    public function format(string $text): string {
        return $this->component->format($text);
    }
}

