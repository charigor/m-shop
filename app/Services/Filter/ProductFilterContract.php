<?php

namespace App\Services\Filter;

use App\Models\Brand;
use App\Services\Filter\SearchRepository;
use Illuminate\Database\Eloquent\Collection;

interface ProductFilterContract
{

    public function build($params);
    public function search();

    public function buildFacets():array;
    public function buildAllFacets():array;
    public function buildDocument();

}
