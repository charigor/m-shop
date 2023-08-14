<?php


namespace App\Services\Cart;


use App\Services\Contracts\CartInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class Cart implements CartInterface
{
    public int|null $cookieCart;
    public \App\Models\Cart $model;
    public \App\Models\CartItem $modelItem;
    public function __construct(){
        $this->model = new \App\Models\Cart();
        $this->modelItem = new \App\Models\CartItem();
        if(Cookie::get('cart')){
            $this->cookieCart = Cookie::get('cart');
        }else{
            $cart = $this->model::create(['customer_id' => Auth::check() ? Auth::user()->id : null]);
            Cookie::queue('cart', $cart->id, 100);
            $this->cookieCart = $cart->id;
        }


    }
    public function add($item){
       $cart =  $this->model->where('id',$this->cookieCart)->first();
       if($cart->cartItems()->where('product_id',$item['product_id'])->exists()){
           $this->addQuantity($item['product_id']);
       }else{
           $cart->cartItems()->create($item);
       }
       return $cart->load('cartItems');
    }
    public function get(){
       return  $this->model->with('cartItems')->where('id',$this->cookieCart)->first();
    }
    public function getItems(){
        return  $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->get();
    }
    public function removeQuantity($product_id)
    {
       $cartItem =  $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->where('product_id',$product_id)->first();
       $cartItem->quantity =  $cartItem->quantity - 1;
       $cartItem->save();
       return $this->get();
    }
    public function addQuantity($product_id){
        $cartItem =  $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->where('product_id',$product_id)->first();
        $cartItem->quantity =  $cartItem->quantity + 1;
        $cartItem->save();
        return $this->get();

    }
    public function removeItem($product_id){
       return  $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->where('product_id',$product_id)->delete();
    }
    public function getTotal(){
        return $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->get()->reduce(function (?int $carry,$item) {

            return $carry + ($item['price'] * $item['quantity']);
        });
    }
    public function getTotalQuantity(){
       return $this->modelItem->whereHas('cart',fn($q) => $q->where('id',$this->cookieCart))->sum('quantity');
    }
    public function clear(){
        $cart =  $this->model->where('id',$this->cookieCart)->first();
        $cart->cartItems()->delete();
    }

    public function getCookieCart(){
        return $this->cookieCart;
    }
}
