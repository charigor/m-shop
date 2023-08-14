<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        $defaultData = parent::toArray($request);
        $additionalData = [
            'translation' => $this->translation->keyBy('locale'),
            'image' => $this->getMedia('image'),
        ];

        return array_merge($defaultData,$additionalData);

    }
}
