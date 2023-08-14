<?php

namespace App\Http\Resources\Admin\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
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
        ];

        return array_merge($defaultData,$additionalData);
    }
}
