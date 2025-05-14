<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'cart_id' => $this['cart_id'],
            'item_count' => $this['item_count'],
            'subtotal' => $this['subtotal'],
            'total' => $this['total'],
            'items' => $this['items'],
        ];
    }
}
