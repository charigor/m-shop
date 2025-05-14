<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeGroupController;
use App\Http\Controllers\Admin\AttributeProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DeleteTemporaryImageController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\FeatureValueController;
use App\Http\Controllers\Admin\LangController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SelectController;
use App\Http\Controllers\Admin\UploadEditorImageController;
use App\Http\Controllers\Admin\UploadTemporaryImageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use App\Models\User;
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

// Route::get('/user', [\App\Http\Controllers\UserController::class ,'index']);

Route::middleware(['auth', 'setLocale'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return Inertia::render('HomeView');
        })->middleware(['auth', 'verified'])->name('dashboard');
        Route::get('/messages', [MessageController::class, 'index'])->name('message.index');
        Route::post('/messages', [MessageController::class, 'store'])->name('message.store');
        Route::post('/messages/notifications', [MessageController::class, 'getNotify'])->name('message.notify');
        Route::post('/messages/mark_read', [MessageController::class, 'markRead'])->name('message.mark_read');
        /* Users */
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::put('/users/{user}/update', [UserController::class, 'update'])->name('user.update');
        Route::post('/users', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/users/delete', [UserController::class, 'destroy'])->name('user.delete');

        /* Roles */
        Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
        Route::put('/roles/{role}/update', [RoleController::class, 'update'])->name('role.update');
        Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/roles/delete', [RoleController::class, 'destroy'])->name('role.delete');

        /* Permissions */
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permission.index');
        Route::put('/permissions/{permission}/update', [PermissionController::class, 'update'])->name('permission.update');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permissions/delete', [PermissionController::class, 'destroy'])->name('permission.delete');

        /* Categories */
        Route::post('/categories/storeMedia', [CategoryController::class, 'storeMedia'])->name('categories.media');
        Route::post('/categories/delete', [CategoryController::class, 'destroy'])->name('categories.delete');
        Route::post('/categories/slug', [CategoryController::class, 'slug'])->name('categories.slug');
        Route::post('/categories/sort', [CategoryController::class, 'sort'])->name('categories.sort');
        Route::resource('categories', CategoryController::class)->except('index', 'show', 'destroy');
        Route::get('/categories/{parent_id?}', [CategoryController::class, 'index'])->name('categories.index');

        Route::post('/upload', UploadTemporaryImageController::class);
        Route::post('/uploadEditorImage', UploadEditorImageController::class);

        Route::delete('/revert/{folder}', DeleteTemporaryImageController::class);

        /* Lang */
        Route::post('/lang/delete', [LangController::class, 'destroy'])->name('lang.delete');
        Route::resource('lang', LangController::class)->except('show', 'destroy');
        /* Product */
        Route::post('/product/{product}/feature/delete', [ProductController::class, 'deleteFeature'])->name('product.feature.delete');
        Route::post('/product/slug', [ProductController::class, 'slug'])->name('product.slug');
        Route::post('/product/delete', [ProductController::class, 'destroy'])->name('product.delete');
        Route::post('/product/storeMedia', [ProductController::class, 'storeMedia'])->name('product.media');
        Route::delete('/product/{product}/removeAttributes', [ProductController::class, 'removeAttributes'])->name('product.attributes.remove');
        Route::resource('product', ProductController::class)->except('show', 'destroy');
        /* Product attribute */
        Route::resource('attribute_product', AttributeProductController::class)->only('store', 'update', 'destroy');
        /* Brand */
        Route::post('/brand/storeMedia', [BrandController::class, 'storeMedia'])->name('brand.media');
        Route::post('/brand/delete', [BrandController::class, 'destroy'])->name('brand.delete');
        Route::resource('brand', BrandController::class)->except('show', 'destroy');

        /* Attribute groups */
        Route::post('/attribute_group/sort', [AttributeGroupController::class, 'sort'])->name('attribute_group.sort');
        Route::post('/attribute_group/delete', [AttributeGroupController::class, 'destroy'])->name('attribute_group.delete');
        Route::resource('attribute_group', AttributeGroupController::class)->except('destroy');

        /* Attribute */
        Route::post('/attribute_group/{id}/attribute/sort', [AttributeController::class, 'sort'])->name('attribute_group.attribute.sort');
        Route::post('/attribute_group/{id}/attribute/delete', [AttributeController::class, 'destroy'])->name('attribute_group.attribute.delete');
        Route::resource('attribute_group.attribute', AttributeController::class)->only('create', 'edit', 'store', 'update');
        /* Feature */
        Route::post('/feature/sort', [FeatureController::class, 'sort'])->name('feature.sort');
        Route::post('/feature/delete', [FeatureController::class, 'destroy'])->name('feature.delete');
        Route::resource('feature', FeatureController::class)->except('destroy');
        /* Feature value */
        Route::post('/feature/{id}/feature_value/delete', [FeatureValueController::class, 'destroy'])->name('feature.feature_value.delete');
        Route::resource('feature.feature_value', FeatureValueController::class)->only('create', 'edit', 'store', 'update');

        Route::get('/select', [SelectController::class, 'load']);
        Route::post('/language', function (Request $request) {
            Session()->put('locale', $request->lang);
            app()->setLocale($request->lang);

            return Response()->json(['locale' => session('locale')]);
        })->name('language');
    });

});
Route::group(['as' => 'front.', 'middleware' => ['setLocale', 'auth']], function () {
    Route::get('/', [MainController::class, 'index'])->name('main');
    Route::get('/test', [TestController::class, 'index'])->name('test');
    Route::get('checkout', [\App\Http\Controllers\Front\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout/stepOne', [\App\Http\Controllers\Front\CheckoutController::class, 'stepOne'])->name('checkout.step.one');
    Route::get('category/{slug}', [\App\Http\Controllers\Front\CategoryController::class, 'show'])->name('category.show');
    Route::get('brand', [\App\Http\Controllers\Front\BrandController::class, 'index'])->name('brand.index');
    Route::get('brand/{brand:slug}', [\App\Http\Controllers\Front\BrandController::class, 'show'])->name('brand.show');

    Route::match(['POST', 'GET'], 'novaposhta/cities', [\App\Http\Controllers\Front\NovaPoshtaController::class, 'getCities']);
    Route::match(['POST', 'GET'], 'novaposhta/warehouses', [\App\Http\Controllers\Front\NovaPoshtaController::class, 'getWarehouses']);
    Route::match(['POST', 'GET'], 'novaposhta/warehouseTypes', [\App\Http\Controllers\Front\NovaPoshtaController::class, 'getWarehouseTypes']);

    Route::get('language/{locale}', function ($locale) {
        app()->setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    })->name('language.locale');
    Route::get('product/{product:link_rewrite}/', [\App\Http\Controllers\Front\ProductController::class, 'show'])->name('product.show');
    Route::get('/get-unique-user-names', [\App\Http\Controllers\Front\UserController::class, 'getUniqueUserNames']);

    Route::get('/login/google', [\App\Http\Controllers\Front\Auth\GoogleLogin::class, 'login']);
    Route::get('/login/google/callback', [\App\Http\Controllers\Front\Auth\GoogleLogin::class, 'callback']);
    Route::get('/login/facebook', [\App\Http\Controllers\Front\Auth\FacebookLogin::class, 'login']);
    Route::get('/login/facebook/callback', [\App\Http\Controllers\Front\Auth\FacebookLogin::class, 'callback']);

    //    Route::view('front.checkout.success')->name('stripe-checkout-success');
    //    Route::view('front.checkout.cancel')->name('stripe-checkout-cancel');
});
Route::get('/stripe-checkout', function (Request $request) {
    $stripePriceId = 'price_1OkjOrIPGZoDS9PBgQFN8aVu';

    $quantity = 1;

    $res = $request->user()->checkout([$stripePriceId => $quantity], [
        'success_url' => url('/'),
        'cancel_url' => url('/'),
    ]);

    $invoice = $request->user()->invoiceFor('Test product', 1, [
        'description' => 'Custom Description', // Optional metadata
        'metadata' => [
            'custom_key' => 'custom_value', // Additional metadata
        ],
        'price_data' => [
            'currency' => 'usd', // Replace with your desired currency
            'unit_amount' => 1, // Replace with the unit amount in cents

        ],
    ]);
})->name('stripe-checkout');
Route::get('/subscription-checkout', function (Request $request) {
    return $request->user()
        ->newSubscription('default', 'price_1OkjchIPGZoDS9PBNqe5uNGU')->withMetadata(['memo' => 'AAAAAAAAAAAAAAAAAAAAAAAA'])
        ->checkout([
            'cancel_url' => url('checkout-cancelled'),
            'success_url' => url('checkout-complete'),
        ]);
});
require __DIR__.'/auth.php';
