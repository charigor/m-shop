<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'cover_image' => $this->getMedia('cover_image'),
            'menu_thumbnail' => $this->getMedia('menu_thumbnail'),
            'children' => $this->children ?? ""
        ];

        return array_merge($defaultData,$additionalData);

    }
}
