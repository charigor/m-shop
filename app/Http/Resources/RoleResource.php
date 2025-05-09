<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'created_at' => isset($this->created_at) ? $this->created_at->format('d-m-Y h:m:s') : null,
            'permissions' => isset($this->permissions) ? $this->permissions : [],
        ];
    }
}
