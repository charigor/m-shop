<?php

namespace App\Services\Datatables\Traits;

use App\Services\Datatables\Filter;
use Illuminate\Http\Request;

trait Datatable
{
    /**
     * Sorting.
     */
    protected function sorting($query, $request, $exceptions = [], $defaultColumn = 'id', $secondColumn = null)
    {
        if ($request->get('order') && ! ($request->get('order')[0]['column'] !== null || ! $request->get('order')[0]['column']) !== '') {
            $column = $request->get('columns')[$request->get('order')[0]['column']]['name'];
            if (! in_array($column, $exceptions)) {
                $dir = $request->get('order')[0]['dir'] ?? 'ASC';
                if ($secondColumn != null) {
                    $query->orderByRaw($column.' '.$dir.', '.$secondColumn.' DESC ');
                } else {
                    $query->orderByRaw($column.' '.$dir.', ISNULL('.$column.') ASC ');
                }
            }
        } else {

            if ($secondColumn != null) {
                $query->orderByRaw($defaultColumn.' DESC, '.$secondColumn.' DESC ');
            } else {
                $query->orderByDesc($defaultColumn);
            }
        }
    }

    /**
     * @return mixed
     */
    public static function datatable(Request $request)
    {
        return (new static())->table($request);
    }

    /**
     * Apply filters.
     *
     * @throws DatatableException
     */
    public function filtering($query, $request)
    {

        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] == true) {
                if ($column['search']['value'] !== null) {
                    if (isset($this->customFilters)) {
                        if (isset($this->customFilters[$column['name']])) {
                            $filter = new $this->customFilters[$column['name']]($column);

                            if ($filter instanceof Filter) {
                                $filter->filter($query);
                            } else {
                                throw new DatatableException('Found Class '.$this->customFilters[$column['name']].' is not Filter Instance');
                            }
                        }
                    }
                }
            }
        }
    }

    public function response(EloquentDataTable $table)
    {

        foreach ($this->customFilters as $key => $filter) {
            $table->filterColumn($key, function ($query, $value) use ($key, $filter) {
                $block = ['search' => ['value' => $value]];
                $filter = new $filter($block);
                if ($filter instanceof Filter) {
                    $filter->filter($query);
                } else {
                    throw new DatatableException('Found Class '.$key.' is not Filter Instance');
                }
            });
        }

        return $table->make(true);
    }

    public function searching($query, $request)
    {
        foreach ($request->get('columns') as $column) {
            if ($column['searchable'] === 'true') {
                $query->orHaving($column['name'], 'like', '%'.$request['search']['value'].'%');
            }
        }

    }

    /**
     * @return mixed
     */
    public function search(Request $request, $query)
    {
        //1 version
        //        if($request->has('query')){
        //            if($request->has('search') && !empty($request->get('search')) && $request->get('query')){
        //
        //                foreach($request->get('search') as $key =>  $item){
        //                   $query =  $query->orHaving($item,'like','%'.$request->get('query').'%');
        //                }
        //            }
        //        }
        if ($request->has('search') && ! empty($request->get('search'))) {
            foreach ($this->search as $item) {
                $query = $query->orHaving($item, 'like', '%'.$request->get('search').'%');
            }
        }

        return $query;
    }

    /**
     * @return mixed
     */
    public function filter(Request $request, $query)
    {
        if ($request->has('filter')) {
            //            info(gettype($request->get('filter')));
            //            $filter = json_decode( $request->get('filter'),true);
            $filter = $request->get('filter');
            foreach ($filter as $key => $name) {
                if (isset($this->filters)) {
                    if (isset($this->filters[$key]) && ! is_null($name)) {

                        $filter = new $this->filters[$key]($name);
                        if ($filter instanceof Filter) {
                            $filter->filter($query);
                        }
                    }
                }
            }
        }

        return $query;
    }

    /**
     * @return mixed
     */
    public function sort(Request $request, $query)
    {

        $sortBy = $request->has('field') ? $request->get('field') : 'id';
        if ($request->has('field')) {
            $query = $request->get('direction') === 'desc' ? $query->orderBy($sortBy, 'desc') : $query->orderBy($sortBy, 'asc');
        } else {
            $query->orderBy($sortBy, 'desc');
        }

        return $query;
    }

    /**
     * @return int|mixed
     */
    public function pagination(Request $request, $query)
    {
        if ($request->has('perPage')) {
            $this->perPage = $request->get('perPage') == '-1' ? $query->get()->count() : $request->get('perPage');
        }

        return $this->perPage;
    }
}
