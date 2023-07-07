<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionCreateRequest;
use App\Http\Requests\Admin\PermissionUpdateRequest;
use App\Http\Resources\PermissionResource;
use App\Services\Datatables\Permissions\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Inertia::render('Permissions/Index', [
            'permissions' => PermissionResource::collection((new Permissions)->table($request)),
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);
    }

    /**
     * @param PermissionCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PermissionCreateRequest $request)
    {

        $permission = Permission::create($request->validated());

        return redirect()->route('permission.edit',$permission->id)->with('message',trans('messages.success.create'));
    }
    /**
     * @return \Inertia\Response
     */
    public function edit(Permission $permission)
    {
        return Inertia::render('Permissions/Edit', [

            'permission' => PermissionResource::make($permission)->resolve(),
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Permissions/Create', [
            'permission' => PermissionResource::make(new Permission())->resolve(),
        ]);
    }

    /**
     * @param PermissionUpdateRequest $request
     * @param Permission $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        return redirect()->route('permission.index')->with('message',trans('messages.success.update'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Permission::whereIn('id',$request->ids)->delete();
        return redirect()->route('permission.index')->with('message',trans('messages.success.delete'));
    }
}
