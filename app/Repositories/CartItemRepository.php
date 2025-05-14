<?php

namespace App\Repositories;

use App\Models\CartItem;

class CartItemRepository
{
    public function getByCartId($cartId)
    {
        return CartItem::where('cart_id', $cartId)->get();
    }

    public function findDuplicate($cartId, $productId, $attributeId)
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->where('attribute_id', $attributeId)
            ->first();
    }

    public function findInCart($id, $cartId): ?CartItem
    {
        return CartItem::where('id', $id)
            ->where('cart_id', $cartId)
            ->first();
    }

    public function deleteById($id): void
    {
        CartItem::destroy($id);
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        $item->quantity = $quantity;
        $item->save();
    }

    public function getCartItems($cartId)
    {
        return CartItem::where('cart_id', $cartId)->get();
    }

    public function deleteAllByCartId($cartId): void
    {
        CartItem::where('cart_id', $cartId)->delete();
    }
}
