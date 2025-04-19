<?php

namespace App\Services\Patterns\Builder;

interface Builder
{
    public function select($table,$fields);
    public function where($field,$operator,$value);
    public function limit($limit);
    public function get();
}
