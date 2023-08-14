<?php

namespace App\Http\Resources\AttributeGroup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeGroupResource extends JsonResource
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
        ];

        return array_merge($defaultData,$additionalData);
    }
}
