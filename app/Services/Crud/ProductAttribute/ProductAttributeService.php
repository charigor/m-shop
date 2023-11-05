<?php

namespace App\Services\Crud\ProductAttribute;

use App\Models\AttributeProduct;

class ProductAttributeService
{
    public array $fields;

    public AttributeProduct $attributeProduct;

    public function __construct(AttributeProduct $attributeProduct)
    {
        $this->model = $attributeProduct;
        $this->fields = $attributeProduct->getFillable();
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $model->update($data);
        $this->setActiveMedia($model, $data['media']);
        $model->refresh();

        return $model;
    }

    public function deleteItem($model): mixed
    {
        if ($model->default) {
            $secondModel = $this->model::where('id', '<>', $model->id)->where('product_id', $model->product_id)->first();
            if ($secondModel) {
                $secondModel->update(['default' => 1]);
            }
        }

        return $model->delete();
    }

    private function setActiveMedia($model, $media): void
    {
        collect($media)->each(function ($value) use ($model) {
            $model->getMedia('image')->where('id', $value['id'])->first()->setCustomProperty('active', $value['active'])->save();
        });
    }
}
