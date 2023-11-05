<?php

namespace App\Services\Filter;

interface ProductFilterContract
{
    public function build($params);

    public function search();

    public function buildFacets(): array;

    public function buildAllFacets(): array;

    public function buildDocument();
}
