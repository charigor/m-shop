<div class="flex ml-auto items-center">
    <button class="{{!$cart ?'pointer-events-none' : ''}}" type="button" wire:click.prevent="$emit('openModal', 'cart-modal')"><i class="mdi mdi-cart text-2xl"></i>
        Cart<span class="text-xs mx-1 leading-2">{{$cartQuantity}}</span>Price<span class="text-xs ml-1 leading-2">{{$cartPrice}}</span>
    </button>
</div>

