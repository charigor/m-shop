<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = null;

        // Get active image using the custom properties filter
        $activeImage = $this->media()
            ->where('collection_name', 'image')
            ->whereJsonContains('custom_properties->active', '1')
            ->first();

        // Fallback to any image if no active ones found
        if (! $activeImage) {
            $activeImage = $this->media()
                ->where('collection_name', 'image')
                ->first();
        }

        if ($activeImage) {
            $imageUrl = $activeImage->preview_url;
        }

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'cost' => $this->cost,
            'rebate' => $this->rebate,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'weight' => $this->weight,
            'default' => $this->default,
            'reference' => $this->reference,
            'attributes' => $this->getLocalizedAttributes(),
            'image' => $imageUrl,
        ];
    }
}
