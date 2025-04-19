<?php

namespace App\Services\Patterns\Builder;

use Illuminate\Support\Facades\DB;

class MysqlBuilder implements Builder
{
    public string $query;
    public string $select;
    public string $where;
    public string $limit;
    public function __construct(){
        $this->reset();

    }
    public function select($table,$fields =['*']): static
    {
        $this->select = "SELECT ".implode(' ',$fields)." FROM ".$table;
        return $this;
    }

    public function where($field,$operator,$value): static
    {
        $this->where = " WHERE ".$field.$operator.$value;
        return $this;
    }
    public function limit($limit): static
    {
        $this->limit = " LIMIT $limit ";
        return $this;
    }
    public function get()
    {
        $this->query = $this->select.$this->where.$this->limit;
        return DB::select($this->query) ;
    }
    public function reset(): void
    {
        $this->query = '';
    }
}
