<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductCreateRequest;
use App\Http\Requests\Admin\Product\ProductUpdateRequest;
use App\Http\Resources\Admin\Product\ProductTableResource;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Feature;
use App\Models\FeatureLang;
use App\Models\FeatureValueLang;
use App\Models\Product;
use App\Models\ProductLang;
use App\Services\Crud\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    use MediaUploadingTrait;
    private ProductService $service;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService  $productService)
    {
        $this->service = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request);
        return Inertia::render('Products/Index', [
            'products' => ProductTableResource::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
            'active_options' => createOptions(Product::ACTIVE,'All'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Products/Create', [
            'product' => ProductResource::make(new Product()),
            'categories' => CategoryResource::collection(Category::with(['translation'])->defaultOrder()->withDepth()->get()),
            'feature_options' => FeatureLang::whereHas('feature.featureValue')->where('locale',app()->getLocale())->get()->map(fn($item) => ['value' => $item->feature_id,'label' => $item->name] ),
            'feature_value_options' => FeatureValueLang::with('featureValue')
                ->where('locale',app()->getLocale())
                ->get()
                ->map(fn($item) => ['value' => $item->feature_value_id,'label' => $item->value,'parent' => $item->featureValue->feature_id]),
            'tax_options' => Product::TAXES,
        ]);
    }

    /**
     * @param ProductCreateRequest $request
     * @return RedirectResponse
     */
    public function store(ProductCreateRequest $request)
    {
      $model =  $this->service->createItem($request);
      return redirect()->route('product.edit',$model->id)->with('message',trans('messages.success.create'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => ProductResource::make($product->load(['media','categories','translation']))->resolve(),
            'categories' => CategoryResource::collection(Category::with(['translation'])->defaultOrder()->withDepth()->get()),
            'feature_options' => FeatureLang::whereHas('feature.featureValue')->where('locale',app()->getLocale())->get()->map(fn($item) => ['value' => $item->feature_id,'label' => $item->name] ),
            'feature_value_options' => FeatureValueLang::with('featureValue')
                ->where('locale',app()->getLocale())
                ->get()
                ->map(fn($item) => ['value' => $item->feature_value_id,'label' => $item->value,'parent' => $item->featureValue->feature_id] ),
            'tax_options' => Product::TAXES,
        ]);
    }

    /**
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $product = $this->service->updateItem($product,$request);
        return redirect()->route('product.edit',$product->id)->with('message',trans('messages.success.update'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $brands =  Product::whereIn('id',$request->ids)->get();
        foreach($brands as $item){
            $item->delete();
        }
        return redirect()->route('brand.index')->with('message',trans('messages.success.delete'));
    }
    /**
     * @param Request $request
     */
    public function slug(Request $request): JsonResponse
    {
        $slug =  $this->service->setSlug(ProductLang::class,'link_rewrite',$request->name);
        return response()->json(['slug' => $slug]);
    }
    public  function storeMedia(Request $request): JsonResponse
    {

        return $this->saveMedia($request);
    }
    public function deleteFeature(Request $request,Product $product): RedirectResponse
    {
         $this->service->deleteProductFeature($product,$request);
        return redirect()->back()->with('message',trans('messages.success.delete'));
    }
}
