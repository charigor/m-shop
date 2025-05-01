<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductCreateRequest;
use App\Http\Requests\Admin\Product\ProductUpdateRequest;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Http\Resources\Admin\Product\ProductTableResource;
use App\Http\Resources\Category\CategoryResource;
use App\Jobs\DeleteElasticsearchProduct;
use App\Jobs\UpdateElasticsearchProduct;
use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\FeatureLang;
use App\Models\FeatureValueLang;
use App\Models\Product;
use App\Models\ProductLang;
use App\Services\Crud\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    use MediaUploadingTrait;

    private ProductService $service;

    public function __construct(ProductService $productService)
    {
        $this->service = $productService;
    }

    public function index(Request $request): \Inertia\Response
    {

        abort_unless(Auth::user()->hasAnyRole(['admin']), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $this->service->getItems($request);

        return Inertia::render('Products/Index', [
            'products' => ProductTableResource::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
            'active_options' => createOptions(Product::ACTIVE, 'All'),
        ]);
    }

    public function create(): \Inertia\Response
    {

        return Inertia::render('Products/Create', [
            'product' => ProductResource::make(new Product()),
            'categories' => CategoryResource::collection(Category::with(['translation'])->defaultOrder()->withDepth()->get()),
            'feature_options' => FeatureLang::whereHas('feature.featureValue')->where('locale', app()->getLocale())->get()->map(fn ($item) => ['value' => $item->feature_id, 'label' => $item->name]),
            'feature_value_options' => FeatureValueLang::with('featureValue')
                ->where('locale', app()->getLocale())
                ->get()
                ->map(fn ($item) => ['value' => $item->feature_value_id, 'label' => $item->value, 'parent' => $item->featureValue->feature_id]),
            'tax_options' => Product::TAXES,
            'attributes' => AttributeGroup::with(['attributes.translate', 'translate'])->get(),
        ]);
    }

    public function store(ProductCreateRequest $request): void
    {
        $model = $this->service->createItem($request);
        UpdateElasticsearchProduct::dispatch($product->id);
        to_route('product.edit', $model->id)->with(['message' => trans('messages.success.create'), 'fragment' => $request->has('hashback') ? $request->hashback : '']);
    }

    public function edit(Product $product): \Inertia\Response
    {
        $response = [
            'product' => ProductResource::make($product->load(['media', 'categories', 'translation', 'attributes.attributes.translate', 'attributes.media']))->resolve(),
            'categories' => CategoryResource::collection(Category::with(['translation'])->defaultOrder()->withDepth()->get()),
            'feature_options' => FeatureLang::whereHas('feature.featureValue')->where('locale', app()->getLocale())->get()->map(fn ($item) => ['value' => $item->feature_id, 'label' => $item->name]),
            'feature_value_options' => FeatureValueLang::with('featureValue')
                ->where('locale', app()->getLocale())
                ->get()
                ->map(fn ($item) => ['value' => $item->feature_value_id, 'label' => $item->value, 'parent' => $item->featureValue->feature_id]),
            'tax_options' => Product::TAXES,
            'attributes' => AttributeGroup::with(['attributes.translate', 'translate'])->get(),
        ];

        return Inertia::render('Products/Edit', $response);
    }

    public function update(ProductUpdateRequest $request, Product $product): void
    {
        $model = $this->service->updateItem($product, $request);

        UpdateElasticsearchProduct::dispatch($product->id);
        to_route('product.edit', $model->id)->with(['message' => trans('messages.success.update'), 'fragment' => $request->has('hashback') ? $request->hashback : '']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $products = Product::whereIn('id', $request->ids)->get();
        foreach ($products as $item) {
            $productId = $item->id;
            $item->delete();
            DeleteElasticsearchProduct::dispatch($productId);

        }

        return redirect()->route('.index')->with('message', trans('messages.success.delete'));
    }

    public function slug(Request $request): JsonResponse
    {
        $slug = $this->service->setSlug(ProductLang::class, 'link_rewrite', $request->name);

        return response()->json(['slug' => $slug]);
    }

    public function storeMedia(Request $request): JsonResponse
    {
        return $this->saveMedia($request);
    }

    public function removeAttributes(Product $product): void
    {
        $this->service->removeAttributes($product);
        to_route('product.edit', $product->id)->with(['message' => trans('messages.success.update')]);
    }
}
