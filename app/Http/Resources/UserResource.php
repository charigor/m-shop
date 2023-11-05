<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $defaultData = parent::toArray($request);
        $additionalData = [
            'roles' => $this->roles,
        ];

        return array_merge($defaultData, $additionalData);
    }
}
