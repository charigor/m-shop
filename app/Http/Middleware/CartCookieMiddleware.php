<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartCookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // If the request has a cart_id attribute, set it as a cookie
        // If the controller has requested to set a cart cookie
        if ($request->attributes->has('set_cart_cookie')) {
            $cartId = $request->attributes->get('set_cart_cookie');
            $response->cookie('cart_id', $cartId, 43200); // Cookie expires in 30 days
        }

        return $response;
    }
}
