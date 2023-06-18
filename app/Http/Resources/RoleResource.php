<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'created_at' => isset($this->created_at) ? $this->created_at  : null,
            'permissions' => isset($this->permissions) ? $this->permissions  : []
        ];
    }
}
