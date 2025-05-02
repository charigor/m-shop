<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Attribute\AttributeResource;
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
            'name' => optional($this->translateWithFallback)->name,
            'price' => $this->price,
            'locale' => optional($this->translateWithFallback)->locale,
            'description' => optional($this->translateWithFallback)->description,
            'meta_description' => optional($this->translateWithFallback)->meta_description,
            'meta_keywords' => optional($this->translateWithFallback)->meta_keywords,
            'meta_title' => optional($this->translateWithFallback)->meta_title,
            'link_rewrite' => optional($this->translateWithFallback)->link_rewrite,
            'preview_url' => $this->media->first()?->preview_url,
            'attributes' => AttributeProductResource::collection($this->attributes)


        ];
    }
}
