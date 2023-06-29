<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserCreateRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Datatables\Users\Users;
use App\Services\Test\TestService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return \Inertia\Response
     */

    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN, '403 Forbidden');
         return Inertia::render('Users/Index', [
            'roles' => RoleResource::collection(Role::all()->prepend(['id' => 0,'name' => 'All']))->resolve(),
            'users' => UserResource::collection((new Users)->table($request)),
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);

    }

    /**
     * @param UserCreateRequest $request
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make( $data['password']);
        $user = User::create($data);
        $user->roles()->attach($request->roles);
        if (!empty($data['avatar'])) {
            $image = array_column($data['avatar'], 'image')[0];
            $path = Storage::path($image);
            $user->addMedia($path)->toMediaCollection('avatars');
        }
        return redirect()->route('user.edit',$user->id);

    }
    /**
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'roles' => RoleResource::collection(Role::query()->select(['id','name'])->get())->resolve(),
            'user' => UserResource::make($user->load('media'))->resolve(),
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => RoleResource::collection(Role::all())->resolve(),
            'user' => UserResource::make(new User())->resolve(),
        ]);
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     */
    public function update(UserUpdateRequest $request, User $user)

    {
        $data = $request->validated();
        if(isset($data['password'])){
            $data['password'] = Hash::make( $data['password']);
        }
        $user->update($data);
        $user->roles()->sync($data['roles']);
        if (!empty($data['avatar'])  && count($data['avatar']) >= 1) {
            $image = array_column($data['avatar'], 'image');
            if(isset($image[0])) {
                $user->clearMediaCollection('avatars');
                $path = Storage::path($image[0]);
                $user->addMedia($path)->toMediaCollection('avatars');
            }else{
                $user->clearMediaCollection('avatars');
            }
        }
        return redirect()->route('user.index');
    }


    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        User::whereIn('id',$request->ids)->delete();
        return redirect()->route('user.index');
    }
}
