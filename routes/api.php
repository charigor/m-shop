<?php

use App\Http\Controllers\api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\Auth\AuthController;
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
Route::post('/login', function (Request $request) {
    return response()->json($request->user());
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return response()->json($request->user());
//});
Route::get('/category', [CategoryController::class,'index']);
Route::get('/search', [App\Http\Controllers\api\SearchController::class, 'search']);
Route::post('/set-locale', function (\Illuminate\Http\Request $request) {
    $lang = $request->input('lang');

    if (in_array($lang, ['en', 'uk'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang); // на всякий случай
    }

    return response()->json(['status' => 'ok']);
});
