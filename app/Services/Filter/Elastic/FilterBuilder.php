<?php

namespace App\Services\Filter\Elastic;

/**
 * Builder class for Elasticsearch filters
 */
class FilterBuilder
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    // Additional filter building methods can be added here
}
