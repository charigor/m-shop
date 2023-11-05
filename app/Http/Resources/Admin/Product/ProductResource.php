<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $defaultData = parent::toArray($request);
        $additionalData = [
            'translation' => $this->translation->keyBy('locale'),
            'categories' => $this->categories()->get(),
            'image' => $this->getMedia('image')->sortBy(function ($media, $key) {
                return $media->getCustomProperty('order');
            })->toArray(),
            'features' => $this->features()->get()->map(function ($item) {
                return $item->pivot;
            })->toArray(),

        ];

        return array_merge($defaultData, $additionalData);

    }
}
