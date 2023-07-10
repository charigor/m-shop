<?php

namespace App\Http\Resources\Lang;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LangResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {

        return [
            'id'               => $this['id'],
            'name'             => $this['name'],
            'code'             => $this['code'],
            'active'           => $this['active'],
            'date_format'      => $this['date_format'],
            'date_format_full' => $this['date_format_full']
        ];
    }
}
