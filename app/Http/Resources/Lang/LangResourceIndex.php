<?php

namespace App\Http\Resources\Lang;

use App\Models\Lang;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LangResourceIndex extends JsonResource
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
            'active'           => Lang::ACTIVE[$this['active']],
            'date_format'      => $this['date_format'],
            'date_format_full' => $this['date_format_full']
        ];
    }
}
