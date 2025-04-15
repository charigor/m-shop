<?php

use App\Http\Controllers\api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/category/{slug}', ['CategoryController::class','index']);
//Route::prefix('admin')->group(function () {
//    Route::get('/users/table', [UserController::class,'table']);
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
//});
