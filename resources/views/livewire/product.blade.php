<div class="flex">
    <div class="mb-5 flex-col w-2/3">
        <x-swiper-slider  :product="$productAttribute ?? $product"></x-swiper-slider>
        <x-product-desc-tabs :product="$product" />
    </div>
    <div class="mb-5 py-5 flex-col w-1/3">
        <h2 class="text-xl mb-2 text-gray-600">{{$product->translate->name}} {{$productAttribute ? $productAttribute->attributes->first()->translate->name: ''}}</h2>
        <div class="mb-2">
            <p class="text-sm"><span>Артикул:</span> {{$productAttribute ? $productAttribute->reference : $product->reference }} </p>
        </div>
        <div class="text-3xl mb-2">{{priceFormat(isset($productAttribute) ? $productAttribute->price : $product->price,2)}} грн</div>
        <div class="flex items-center"><i class="mdi mdi-sale-outline text-red-800 text-2xl mr-2"></i><p class="text-xs text-red-800">Отримай знижку 5% за реєстрацію на сайті</p></div>

        <x-product-attributes :attr="$attributes" />

        <div class="my-2">
            <button wire:click.debounce.250ms="addToCart({{$product->id}},{{$productAttribute ? $productAttribute->id : null}})" type="button"
                    class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-1.5 mr-1 mb-1 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                Купити
            </button>
        </div>

        <x-info-tabs />
    </div>
</div>
