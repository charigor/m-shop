<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $defaultData = parent::toArray($request);
        $additionalData = [
//            'media'=> $this->whenLoaded('media'),
            'roles' => $this->roles
        ];
        return array_merge($defaultData,$additionalData);
    }
}
