<?php

namespace App\Http\Resources\Brand;


use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResourceIndex extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {

        $default =  parent::toArray($request);
        $default['active'] =  trans('global.'.lcfirst(Brand::ACTIVE[$this['active']]));
        return  $default;

    }

}
