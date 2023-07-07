<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DeleteTemporaryImageController;
use App\Http\Controllers\Admin\LangController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\UploadEditorImageController;
use App\Http\Controllers\Admin\UploadTemporaryImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('HomeView');
//    return Inertia::render('Welcome');
//    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');;

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('admin')->group(function () {
        Route::get('/messages', [MessageController::class, 'index'])->name('message.index');
        Route::post('/messages', [MessageController::class, 'store'])->name('message.store');

        /*Users */
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::put('/users/{user}/update', [UserController::class, 'update'])->name('user.update');
        Route::post('/users', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/users/delete', [UserController::class, 'destroy'])->name('user.delete');

        /*Roles */
        Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
        Route::put('/roles/{role}/update', [RoleController::class, 'update'])->name('role.update');
        Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/roles/delete', [RoleController::class, 'destroy'])->name('role.delete');

        /*Permissions */
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permission.index');
        Route::put('/permissions/{permission}/update', [PermissionController::class, 'update'])->name('permission.update');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permissions/delete', [PermissionController::class, 'destroy'])->name('permission.delete');

        /*Categories*/
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/categories/sort', [CategoryController::class, 'sort'])->name('category.sort');
        Route::get('/categories/{parent_id?}', [CategoryController::class, 'index'])->name('category.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
        Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('category.update');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::post('/categories/storeMedia', [CategoryController::class,'storeMedia']);
        Route::post('/categories/delete', [CategoryController::class, 'destroy'])->name('category.delete');

        Route::get('/test', [TestController::class, 'index'])->name('test.index');


        Route::post('/upload', UploadTemporaryImageController::class);
        Route::post('/uploadEditorImage', UploadEditorImageController::class);

        Route::delete('/revert/{folder}', DeleteTemporaryImageController::class);

        /*Lang*/
        Route::post('/lang/delete', [LangController::class, 'destroy'])->name('lang.delete');
        Route::resource('lang', LangController::class)->except('show','destroy');
        Route::post('/language', function(Request $request){
            Session()->put('locale',$request->lang);
//            info($request->all());
//            app()->setLocale($request->code);
            return Response()->json(['locale' => session('locale')]);
        })->name('language');
    });


//    Route::prefix('admin')->group(function () {
//        Route::get('/users/table', [UserController::class,'table']);
//        Route::delete('/users/delete-many', [UserController::class,'destroyMany']);
//        Route::apiResource('users', UserController::class);
//
//
//        Route::get('/roles/table', [RoleController::class,'table']);
//        Route::delete('/roles/delete-many', [RoleController::class,'destroyMany']);
//        Route::apiResource('roles', RoleController::class);
//
//        Route::get('/permissions/table', [PermissionController::class,'table']);
//        Route::delete('/permissions/delete-many', [PermissionController::class,'destroyMany']);
//        Route::apiResource('permissions', PermissionController::class);
//    });
});

require __DIR__.'/auth.php';
