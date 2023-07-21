<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Services\Crud\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    use MediaUploadingTrait;
    private ProductService $service;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService   $productService)
    {
        $this->service = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Products/Create', [
            'product' => ProductResource::make(new Product()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $model =  $this->service->createItem($request);
      return redirect()->route('product.edit',$model->id)->with('message',trans('messages.success.create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => ProductResource::make($product->load(['media']))->resolve(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->service->updateItem($product,$request);
        return redirect()->route('product.edit',$product->id)->with('message',trans('messages.success.update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public  function storeMedia(Request $request): JsonResponse
    {

        return $this->saveMedia($request);
    }
}
