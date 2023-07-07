<?php


namespace App\Services\Datatables\Roles;


use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\Datatables\Roles\Filters\Name;
use App\Services\Datatables\Roles\Filters\Guard_name;
use App\Services\Datatables\Roles\Filters\Id;
use App\Services\Datatables\Roles\Filters\Created_at;
use App\Models\Role;

class Roles
{
    use Datatable;
     protected int $perPage = 25;
     protected array $filters = [
         'id'          => Id::class,
         'name'        => Name::class,
         'guard_name'  => Guard_name::class,
         'created_at'  => Created_at::class
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
        return Role::selectRaw('roles.id,roles.name,roles.guard_name,roles.created_at');
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
        $query = $this->sort($request,$query);
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}
