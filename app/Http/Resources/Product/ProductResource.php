<?php

namespace App\Http\Resources\Product;

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
            'image' => $this->getMedia('image')->sortBy(function ($media, $key) {
                return $media->getCustomProperty('order');
            })->toArray(),
        ];

        return array_merge($defaultData,$additionalData);

    }
}
