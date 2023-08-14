<?php

namespace App\Http\Resources\Admin\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $default =  parent::toArray($request);
        $default['amount'] =  $this->amount;

        return $default;
    }
}
