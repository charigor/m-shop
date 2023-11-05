<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray(Request $request)
    {
        $defaultData = parent::toArray($request);

        $additionalData = [
            'translation' => $this->translation->keyBy('locale'),
            'group' => $this->group?->translation->keyBy('locale'),
        ];

        return array_merge($defaultData, $additionalData);

    }
}
