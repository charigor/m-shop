<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleCreateRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Services\Datatables\Roles\Roles;
use App\Services\Datatables\Users\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{


    /**
     * @param Request $request
     * @return \Inertia\Response
     */

    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection((new Roles)->table($request)),
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);
    }

    /**
     * @param RoleCreateRequest $request
     * @return RoleResource
     */
    public function store(RoleCreateRequest $request)
    {
        $data = $request->validated();
        $role = Role::create($data);
        return redirect()->route('role.edit',$role->id);

    }
    /**
     * @return \Inertia\Response
     */
    public function edit(Role $role)
    {
        return Inertia::render('Roles/Edit', [
            'role' => RoleResource::make($role)->resolve(),
            'permissions' => PermissionResource::collection(Permission::query()->select(['id','name'])->get())->resolve(),
        ]);
    }
    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Roles/Create', [
            'role' => RoleResource::make(new Role())->resolve(),
        ]);
    }

    /**
     * @param RoleUpdateRequest $request
     * @param Role $role
     * @return RoleResource
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role->permissions()->sync($request->permissions);
        return redirect()->route('role.index');
    }

    /**
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Role::whereIn('id',$request->ids)->delete();
        return redirect()->route('role.index');
    }
}
