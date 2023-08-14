<?php

namespace App\Http\Resources\AttributeGroup;


use App\Models\AttributeGroup;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeGroupResourceIndex extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request)
    {

        $default =  parent::toArray($request);
        $default['is_color_group'] =  trans('global.'.lcfirst(AttributeGroup::IS_COLOR_GROUP[$this['is_color_group']]));
        $default['group_type'] =  AttributeGroup::GROUP_TYPE[$this['group_type']];
        $default['amount'] =  $this->amount;
        return  $default;

    }

}
