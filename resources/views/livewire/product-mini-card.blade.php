<div
    x-data="{ open: false }"
    :class="{'z-50': open === true  }"
    class=" border-transparent m-[-1px] bg-white border h-100 sm: md:w-[33%] lg:w-[25%] xl:w-[20%] text-center filter relative  transition-all hover:!grayscale-0 relative {{!$productCard['quantity'] ? 'grayscale': '' }}"
    @mouseleave="open = false" @mouseover="open = true"
>
    <div  :class="{'shadow-product  z-5': open === true  }">
        <div class="relative">
            <a href="{{route('front.product.show',$productCard['link_rewrite']).(count($productCard['attribute']) ? $productCard['attribute']['q'] : '')}}"
               class="block bg-transparent z-1 text-decoration-none text-center overflow-hidden border-0 relative h-[250px]">
                <div
                    class="flex items-center justify-center absolute overflow-hidden left-0 right-0 top-0 bottom-0">
                    <img width="250px" height="250px"
                         class="mh-100 text-center mw-100 h-auto w-auto text-sm"
                         src="{{count($productCard['attribute']) ? $productCard['attribute']['image']['preview_url'] : $productCard['image']['preview_url']}}"
                         alt="product image"/>
                </div>
            </a>
        </div>
        <div class="bg-white text-center  m-[4px]">
            <div class="text-center m-[4px] relative">
                <div class="inline-block">
                    <div class="text-xs">
                        Артикул: {{count($productCard['attribute']) ? $productCard['attribute']['reference'] : $productCard['reference']}}</div>
                </div>
                <div class="inline-block ml-[5px]">
                    {{--                                    <div>stars</div>--}}
                </div>
                <div class="inline-block text-xs">
                    {{--                                    comments--}}
                </div>
            </div>
            <div
                class="overflow-hidden h-[64px] mh-3 pb-1 my-[6px] text-sm text-clip center leading-5 ">
                <a href="{{route('front.product.show',$productCard['link_rewrite'])}}"
                   class="block bg-transparent z-1 text-decoration-none text-center overflow-hidden border-0 relative h-[250px]">
                    {{$productCard['name']}} {{count($productCard['attribute']) ? $productCard['attribute']['name'] : ''}}

                </a>
            </div>
            <div class="flex items-center justify-center my-[10px]">

                <span class="text-md font-bold text-gray-700 align-self-center leading-5 mr-[10px]">
                        {{priceFormat(count($productCard['attribute']) ? $productCard['attribute']['cost'] : $productCard['cost'])}}

                     <span>грн</span>
                </span>


                @if(count($productCard['attribute']))
                    @if((int) $productCard['attribute']['rebate'])
                        <span
                            class="text-md font-normal text-gray-400 align-self-center line-through leading-5 mr-[10px]">
                                {{priceFormat($productCard['attribute']['price'])}} <span>грн</span>
                        </span>
                    @endif
                @else
                    @if((int) $productCard['rebate'])
                        <span
                            class="text-md font-normal text-gray-400 align-self-center line-through leading-5 mr-[10px]">
                                {{priceFormat($productCard['price'])}} <span>грн</span>
                        </span>
                    @endif
                @endif


            </div>
        </div>
    </div>
    <div
        class="absolute border-top-0 border border-transparent shadow-product box-border z-0 width-[100%] mt-[-1px] left-0 right-0 top-[calc(100%-10px)] bg-white" x-show="open"
    >
        @if(count($productCard['allAttributes']))
            @foreach($productCard['allAttributes'] as $key => $attribute)
                <div class="mb-4 mt-2">
                    <span class="block text-center text-xs">{{$key}}: </span>
                    @foreach($attribute as $item)
                        <span>
                        <input class="hidden peer"
                               id="attr_{{$productCard['id'].'_'.$item['id'].'_'.$item['product_attribute_id']}}"
                               type="radio" name="{{$productCard['id'].'_'.$key}}"
                               value="{{$item['product_attribute_id']}}" wire:model.debounce.250ms="activeAttribute">
                        <label
                            class="ml-2 cursor-pointer border px-1 dark:hover:text-cyan-500 dark:peer-checked:text-cyan-600   peer-checked:border-cyan-600 hover:text-cyan-500 peer-checked:text-cyan-600"
                            for="attr_{{$productCard['id'].'_'.$item['id'].'_'.$item['product_attribute_id']}}">
                            <span class="text-sm ml-1">{{$item['name']}}</span>
                        </label>
                    </span>
                    @endforeach
                </div>
            @endforeach
        @endif
        <div class="mt-2">
            <div>
                <button wire:click="addToCart({{$productCard['id']}},{{$activeAttribute}})" type="button"
                        class="focus:outline-none mb-5 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-1.5 mr-1 mb-1 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                    Add to cart
                </button>
            </div>
        </div>
    </div>

</div>
