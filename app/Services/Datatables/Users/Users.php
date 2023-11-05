<?php

namespace App\Services\Datatables\Users;

use App\Models\User;
use App\Services\Datatables\Traits\Datatable;
use App\Services\Datatables\Users\Filters\Created_at;
use App\Services\Datatables\Users\Filters\Email;
use App\Services\Datatables\Users\Filters\Id;
use App\Services\Datatables\Users\Filters\Name;
use App\Services\Datatables\Users\Filters\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Users
{
    use Datatable;

    protected int $perPage = 25;

    protected array $filters = [
        'id' => Id::class,
        'name' => Name::class,
        'email' => Email::class,
        'roles' => Roles::class,
        'created_at' => Created_at::class,
    ];

    protected array $search = [
        'name',
        'email',
        'roles',
    ];

    protected array $sort = [
        'id',
        'name',
        'email',
        'created_at',
    ];

    /**
     * @return Builder
     */
    public function query()
    {
        return User::with('media')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles as role', 'role.id', '=', 'model_has_roles.role_id', 'left outer')
            ->selectRaw('DISTINCT users.id,users.name,users.email,group_concat(role.name) as roles,users.created_at')
            ->groupBy('users.id');
    }

    /**
     * @return mixed
     */
    public function table(Request $request)
    {
        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',', $this->sort)],
        ]);
        $query = $this->query();
        $query = $this->filter($request, $query);
        $query = $this->search($request, $query);
        $query = $this->sort($request, $query);
        $this->perPage = $this->pagination($request, $query);

        return $query->paginate($this->perPage);

    }
}
