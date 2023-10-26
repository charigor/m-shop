<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttributeProductUpdateRequest;
use App\Models\AttributeProduct;
use App\Services\Crud\ProductAttribute\ProductAttributeService;
use Illuminate\Http\Request;

class AttributeProductController extends Controller
{
    use MediaUploadingTrait;

    private ProductAttributeService $service;

    /**
     * @param ProductAttributeService $productService
     */
    public function __construct(ProductAttributeService $productService)
    {
        $this->service = $productService;
    }

    /**
     * @param AttributeProductUpdateRequest $request
     * @param AttributeProduct $attributeProduct
     * @return void
     */
    public function update(AttributeProductUpdateRequest $request, AttributeProduct $attributeProduct): void
    {
        $this->service->updateItem($attributeProduct,$request);
        to_route('product.edit',$attributeProduct->product_id)->with(['message' => trans('messages.success.update'),'fragment' => '#type']);
    }

    /**
     * @param AttributeProduct $attributeProduct
     * @return void
     */
    public function destroy(AttributeProduct $attributeProduct): void
    {
        $this->service->deleteItem($attributeProduct);
        to_route('product.edit',$attributeProduct->product_id)->with(['message' => trans('messages.success.delete'),'fragment' => '#type']);
    }
}
