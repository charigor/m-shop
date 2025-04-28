<?php

namespace App\Http\Resources\Api;

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
        return [
            'id' => $this->id,
            'name' => optional($this->translate)->name,
            'price' => $this->price,
            'locale' => optional($this->translate)->locale,
            'description' => optional($this->translate)->description,
            'meta_description' => optional($this->translate)->meta_description,
            'meta_keywords' => optional($this->translate)->meta_keywords,
            'meta_title' => optional($this->translate)->meta_title,
            'link_rewrite' => optional($this->translate)->link_rewrite,
            'preview_url' => $this->media->first()?->preview_url,
            'brand_name' => $this->brand->name,
            'brand_id' => $this->brand->id
        ];
    }
}
