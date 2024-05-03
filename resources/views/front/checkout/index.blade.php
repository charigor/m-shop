@extends('layouts.main')

    @section('header')
        <div class="checkout-layout">
            <div class="bg-cyan-600 text-main-light dark:bg-main-dark dark:text-main-light">
                <div class="bg-light-100 gap-4">
                    <div class="container mx-auto">
                        <div class="items-center flex">
                            <a href="/" class="items-start">
                                <h1 class="font-bold text-2xl text-yellow-200">M-SHOP</h1>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('content')
        <div class="checkout-layout">
            <div class="w-full gap-4 border p-5">
                <h1>@lang('Оформлення замовлення')</h1>
                <div>
                    <checkout-component class="col-span-9 col-start-1 col-end-10"></checkout-component>
                    <div class="col-span-3 col-start-10 col-end-13  flex items-center justify-end border">
                        Checkout
                    </div>
                </div>
            </div>
        </div>

    @endsection
</div>
<script>
    window.DELIVERY_TYPE_OPTIONS = {!! json_encode($deliveryTypeOptions) !!}
    window.AUTH = {!! json_encode(auth()->check()) !!}
    window.USER = {!! json_encode(auth()->user()) !!}
</script>



