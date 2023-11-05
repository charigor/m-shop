<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResourceIndex extends JsonResource
{
    /**
     * @return array
     */
    public function toArray(Request $request)
    {
        return parent::toArray($request);
    }
}
