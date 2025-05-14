<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    /**
     * Display the cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $data = $this->cartService->getCartData($cart);

        return response()->json([
            'success' => true,
            'data' => new CartResource($data),
        ]);
    }

    /**
     * Add an item to the cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'attribute_id' => 'nullable|numeric',
        ]);

        try {
            $data = $this->cartService->addItemToCart($request);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove cart item quantities
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|numeric|exists:cart_items,id',
        ]);

        try {
            $data = $this->cartService->removeItemFromCart($request);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $status = $e->getCode() === 404 ? 404 : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Update cart item quantities
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|numeric|exists:cart_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        try {
            $data = $this->cartService->syncCart($request);

            return response()->json([
                'success' => true,
                'message' => 'Cart synchronized successfully',
                'data' => new CartResource($data),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function clear(Request $request)
    {
        try {
            $this->cartService->clearCart($request);

            return response()->json([
                'success' => true,
                'message' => 'Корзина успешно очищена.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: '.$e->getMessage(),
            ], 500);
        }
    }
}
