<?php

namespace App\Http\Resources\Admin\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $defaultData = parent::toArray($request);
        $defaultData['active'] = Product::ACTIVE[$this['active']];
        $additionalData = [
            'translation' => $this->translation->keyBy('locale'),
            'media' => $this->media()->get()->filter(function ($item){
                return $item->custom_properties['main_image'] === 1 ||
                       $item->custom_properties['order'] === 0;}
            )->toArray()
        ];

        return array_merge($defaultData,$additionalData);
    }
}
