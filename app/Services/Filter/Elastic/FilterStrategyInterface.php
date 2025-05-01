<?php

namespace App\Services\Filter\Elastic;

/**
 * Interface for all filter strategies
 */
interface FilterStrategyInterface
{
    /**
     * Process input filter value
     */
    public function processInput($input);

    /**
     * Build Elasticsearch filter
     */
    public function buildFilter($processedInput);
}
