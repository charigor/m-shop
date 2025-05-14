<?php

namespace App\Repositories;

use App\Models\Cart;

class CartRepository
{
    public function findByCustomerId($customerId): ?Cart
    {
        return Cart::where('customer_id', $customerId)->first();
    }

    public function findById($id): ?Cart
    {
        return Cart::find($id);
    }

    public function create(array $attributes = []): Cart
    {
        return Cart::create($attributes);
    }

    public function delete($id): void
    {
        Cart::destroy($id);
    }

    public function detachCartFromUser(int $userId): ?Cart
    {
        $cart = Cart::where('customer_id', $userId)->first();

        if ($cart) {
            $cart->customer_id = null;
            $cart->save();
        }

        return $cart;
    }
}
