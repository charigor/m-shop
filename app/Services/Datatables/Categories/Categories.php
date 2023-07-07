<?php


namespace App\Services\Datatables\Categories;


use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\Datatables\Categories\Filters\Id;
use App\Services\Datatables\Categories\Filters\Name;
use App\Services\Datatables\Categories\Filters\Created_at;
use App\Models\Category;

class Categories
{
    use Datatable;
     protected mixed $param = null;
     protected int $perPage = 25;
     protected array $filters = [
         'id'          => Id::class,
         'name'        => Name::class,
         'created_at'  => Created_at::class,
     ];

    protected array $search = [
        'name'
    ];
    protected array $sort = [
        'id',
        'name',
        'created_at',
    ];
    /**
     * @return Builder
     */
    public function query(): Builder
    {

        return Category::with('children')->selectRaw('categories.id,categories.name,categories.created_at,categories.parent_id')->where('categories.parent_id',$this->param);
    }

    /**
     * @param Request $request
     * @return mixed
     */
     public function table(Request $request,$param = null)
    {

        $this->param = $param;
        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',',$this->sort)]
        ]);
        $query = $this->query();
        $query = $this->filter($request,$query);
        $query = $this->search($request,$query);
        $query = $request->has('direction') ? $this->sort($request,$query) : $query->defaultOrder();
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}
