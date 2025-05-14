<?php

use App\Http\Controllers\api\BreadcrumbsController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\CatalogController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\Front\Auth\AuthController;
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
Route::prefix('cart')->middleware(['cartCookie'])->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'add']);
    Route::delete('/remove', [CartController::class, 'remove']);
    Route::post('/sync', [CartController::class, 'sync']);
    Route::delete('/clear', [CartController::class, 'clear']);
});
Route::post('/login', function (Request $request) {
    return response()->json($request->user());
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return response()->json($request->user());
// });
Route::get('/breadcrumbs/translations', [BreadcrumbsController::class, 'getBreadcrumbsTranslations']);
Route::get('/breadcrumbs/category', [BreadcrumbsController::class, 'getCategoryContext']);
Route::post('/breadcrumbs/resolve', [BreadcrumbsController::class, 'resolvePath']);

Route::get('/category/', [CategoryController::class, 'index']);
Route::get('/category/{link_rewrite}/exist', [CategoryController::class, 'exist']);
Route::post('/category/localized-url', [CategoryController::class, 'getLocalizedUrl']);
Route::get('/catalog/{link_rewrite?}', [CatalogController::class, 'index']);
Route::get('/search', [App\Http\Controllers\api\SearchController::class, 'search']);
Route::get('/product/{link_rewrite}', [ProductController::class, 'show']);

Route::post('/set-locale', function (Request $request) {
    $lang = $request->input('lang');

    if (in_array($lang, ['en', 'uk'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang); // на всякий случай
    }

    return response()->json(['status' => 'ok']);
});
