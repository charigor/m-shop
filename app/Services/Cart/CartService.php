<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        protected CartRepository $cartRepo,
        protected CartItemRepository $itemRepo
    ) {}

    public function getOrCreateCart(Request $request): ?\App\Models\Cart
    {
        $user = $request->user();
        $guestCartId = $request->cookie('cart_id');

        if ($user) {
            $userCart = $this->cartRepo->findByCustomerId($user->id)
                ?? $this->cartRepo->create(['customer_id' => $user->id]);

            if ($guestCartId && $guestCart = $this->cartRepo->findById($guestCartId)) {
                if ($guestCart->id !== $userCart->id) {
                    $this->mergeGuestCart($guestCart->id, $userCart->id);
                }
            }

            return $userCart;
        }

        $cart = $guestCartId
            ? $this->cartRepo->findById($guestCartId)
            : null;

        if (! $cart) {
            $cart = $this->cartRepo->create();
            $request->attributes->add(['set_cart_cookie' => $cart->id]);
        }

        return $cart;
    }

    public function mergeGuestCart($guestCartId, $userCartId): void
    {
        $guestItems = $this->itemRepo->getByCartId($guestCartId);

        foreach ($guestItems as $guestItem) {
            $existing = $this->itemRepo->findDuplicate($userCartId, $guestItem->product_id, $guestItem->attribute_id);

            if ($existing) {
                $existing->quantity += $guestItem->quantity;
                $existing->save();
                $guestItem->delete();
            } else {
                $guestItem->cart_id = $userCartId;
                $guestItem->save();
            }
        }

        $this->cartRepo->delete($guestCartId);
    }

    public function getCartData($cart): array
    {
        $items = $this->itemRepo->getByCartId($cart->id);
        $subtotal = $items->sum(fn ($item) => $item->price * $item->quantity);

        return [
            'cart_id' => $cart->id,
            'items' => $items,
            'item_count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }

    public function syncCart(Request $request): array
    {
        $cart = $this->getOrCreateCart($request);

        DB::beginTransaction();

        try {
            foreach ($request->items as $itemData) {
                $item = $this->itemRepo->findInCart($itemData['id'], $cart->id);

                if ($item) {
                    if ($itemData['quantity'] <= 0) {
                        $this->itemRepo->deleteById($item->id);
                    } else {
                        $this->itemRepo->updateQuantity($item, $itemData['quantity']);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $items = $this->itemRepo->getCartItems($cart->id);
        $subtotal = $items->sum(fn ($item) => $item->price * $item->quantity);

        return [
            'cart_id' => $cart->id,
            'items' => $items,
            'item_count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }

    public function removeItemFromCart(Request $request): array
    {
        $cart = $this->getOrCreateCart($request);

        $item = $this->itemRepo->findInCart($request->item_id, $cart->id);

        if (! $item) {
            throw new \Exception('Cart item not found', 404);
        }

        $item->delete();

        $cartItems = $this->itemRepo->getCartItems($cart->id);
        $itemCount = $cartItems->sum('quantity');
        $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

        return [
            'item_count' => $itemCount,
            'total' => $total,
        ];
    }

    public function addItemToCart(Request $request): array
    {
        $cart = $this->getOrCreateCart($request);
        $product = Product::findOrFail($request->product_id);

        DB::beginTransaction();

        try {
            $existingItem = $this->itemRepo->findDuplicate(
                $cart->id,
                $product->id,
                $request->attribute_id
            );

            if ($existingItem) {
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
                $item = $existingItem;
            } else {
                $item = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'name' => $product->translate->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'image' => $product->mainImage->preview_url,
                    'attribute_id' => $request->attribute_id,
                ]);
            }

            DB::commit();

            $cartItems = $this->itemRepo->getCartItems($cart->id);
            $itemCount = $cartItems->sum('quantity');
            $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

            return [
                'item' => $item,
                'item_count' => $itemCount,
                'total' => $total,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function clearCart(Request $request): void
    {
        $cart = $this->getOrCreateCart($request);

        DB::beginTransaction();

        try {
            $this->itemRepo->deleteAllByCartId($cart->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function handleLogout(Request $request): ?Cart
    {
        $user = $request->user();

        if (! $user) {
            throw new \Exception('No authenticated user');
        }

        return $this->cartRepo->detachCartFromUser($user->id);
    }
}
