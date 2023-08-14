<?php


namespace App\Services\Datatables\Brands;


use App\Models\Brand;
use App\Services\Datatables\Brands\Filters\Active;
use App\Services\Datatables\Brands\Filters\Created_at;
use App\Services\Datatables\Brands\Filters\Id;
use App\Services\Datatables\Brands\Filters\Name;
use App\Services\Datatables\Brands\Filters\Updated_at;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class Brands
{
    use Datatable;
    protected int $perPage = 25;
    protected array $filters = [
        'id'               => Id::class,
        'name'             => Name::class,
        'active'           => Active::class,
        'created_at'       => Created_at::class,
        'updated_at'       => Updated_at::class,
    ];

    protected array $search = [
        'name',
        'active',
    ];
     protected array $sort = [
         'id',
         'name',
         'active',
         'created_at',
         'updated_at',
     ];
    /**
     * @return Builder
     */
    public function query(): Builder
    {

        return Brand::with('media')->selectRaw('brands.id,brands.name,brands.active,brands.created_at,brands.updated_at');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function table(Request $request)
    {

        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',',$this->sort)]
        ]);
        $query = $this->query();
        $query = $this->filter($request,$query);
        $query = $this->search($request,$query);
        $query = $request->has('direction') ? $this->sort($request,$query) : $query->orderByDesc('id');
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}

