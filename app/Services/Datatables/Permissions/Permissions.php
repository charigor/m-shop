<?php


namespace App\Services\Datatables\Permissions;


use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\Datatables\Permissions\Filters\Name;
use App\Services\Datatables\Permissions\Filters\Guard_name;
use App\Services\Datatables\Permissions\Filters\Id;
use App\Services\Datatables\Permissions\Filters\Created_at;
use App\Models\Permission;

class Permissions
{
    use Datatable;
     protected $perPage = 10;
     protected $filters = [
         'id'          => Id::class,
         'name'        => Name::class,
         'guard_name'  => Guard_name::class,
         'created_at'  => Created_at::class
     ];
    protected $search = [
        'name'
    ];

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return Permission::selectRaw('permissions.id,permissions.name,permissions.guard_name,permissions.created_at');
    }

    /**
     * @param Request $request
     * @return mixed
     */
     public function table(Request $request)
    {
        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:id,name,created_at']
        ]);
        $query = $this->query();
        $query = $this->filter($request,$query);
        $query = $this->search($request,$query);
        $query = $this->sort($request,$query);
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}
