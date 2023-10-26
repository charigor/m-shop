<div class="mb-5 flex-column">
    <div class="mt-4 w-100 flex">
        <div class="w-[250px] ml-[30px] p-2">
            <h1 class="text-xl">{{$pageTitle}}</h1>
        </div>
        <div class="flex items-center justify-end" style="width: calc(100% - 250px)">
            <div class="flex  items-center">
                <span class="text-sm">Сортування:</span>
                <div
                    class="whitespace-nowrap overflow-hidden flex border-2 border-color-gray-200 rounded-lg ml-4 text-sm">
                        <span class="relative px-2 py-1 {{$sortBy === 'price:asc' ? 'bg-purple-200' : ''}}"><label
                                class="cursor-pointer"><input wire:model="sortBy" class="hidden" value="price:asc"
                                                              type="radio">Спочатку дешевше</label></span>
                    <span class="relative px-2 py-1 {{$sortBy === 'price:desc' ? 'bg-purple-200' : ''}}"><label
                            class="cursor-pointer "><input wire:model="sortBy" class="hidden" value="price:desc"
                                                           type="radio">Спочатку дорожче</label></span>
                    {{--                    <span class="relative px-2 py-1 {{$sortBy === 'name' ? 'bg-purple-200' : ''}}"><label--}}
                    {{--                            class="cursor-pointer"><input wire:model="sortBy" class="hidden" value="name" type="radio">За назвою</label></span>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 w-100 flex">
        <div class="w-[250px] p-2">
            <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">

                <div class="bg-light-100 text-main-dark">

                    <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
                        <div class="bg-light-100 text-main-dark ml-[30px] flex-wrap">
                            <x-filter-navigation :nav="$filter" :translation="$filterTrans"/>
                        </div>
                    </div>
                    <div class="bg-main-light mb-2 text-main-dark dark:bg-main-dark dark:text-main-light">
                        <div class="bg-light-100 text-main-dark ml-[30px]">
                            <div
                                class="font-semibold rounded-lg text-md  py-2.5 text-left inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                {{trans('page/product.fields.price')}}
                            </div>
                            <div class="text-sm">Price max : <b>{{$price['max']}}</b></div>
                            <div class="relative flex w-100  items-center">
                                <input type="range" min="{{$price['min']}}" max="{{$maxPrice}}"
                                       wire:model.debounce.250ms="price.max">
                            </div>
                        </div>
                    </div>
                    @foreach($facet as $key => $item)
                        @if(count($item))
                        <div x-data="{ open: true }">
                            <button
                                @click.prevent="open = !open"
                                class="font-semibold rounded-lg text-md px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                type="button">
                                <svg x-bind:class="!open ? 'rotate-0' : ''" class="rotate-90 w-2.5 h-2.5 mr-3"
                                     aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <b>{{$key}}</b>
                            </button>
                            <!-- Dropdown menu -->
                            <div class="z-10  bg-white ml-3"
                                 x-show="open"
                                 x-transition:enter="transition-transform transition-opacity ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform translate-y-100"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-end="opacity-0 transform -translate-y-100"
                            >
                                <ul class="text-sm text-gray-700 dark:text-gray-200"
                                    aria-labelledby="dropdownDefaultButton">
                                    @foreach($item as $k => $element)
                                        <li class="px-1 ml-4">
                                            <div class="relative flex items-center">
                                                <input
                                                    id="checkbox-item-{{$element['guard_name'] === 'brand' ? $element['value'] : $element['id']}}"
                                                    {{!$element['count'] ? 'disabled': '' }}  wire:model.debounce.250ms="filter.{{$element['guard_name']}}.{{$element['value']}}"
                                                    type="checkbox"
                                                    value="{{$element['guard_name'] === 'brand'? $element['value'] : $element['id']}}"
                                                    class="w-4 h-4 cursor-pointer  disabled:opacity-25 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                                <label
                                                    for="checkbox-item-{{$element['guard_name'] === 'brand' ? $element['value'] : $element['id']}}"
                                                    {{!$element['count'] ? 'disabled': '' }}
                                                    class="disabled:opacity-25 flex cursor-pointer font-semibold items-center p-[7px] hover:text-cyan-80 border-0 text-md leading-3 text-black">{{$element['value']}}
                                                    <span class="ml-2 mb-[2px] text-xs text-cyan-600">({{$element['count']}})</span></label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div style="width: calc(100% - 250px)" class="mt-4">
            <div class="flex flex-wrap container">
                @foreach($hits as $product)
                    <div class="z-1 h-100 sm: md:w-[25%] lg:w-[20%] xl:w-[16.66667%] relative">
                        <div class="m-[4px] relative">
                            <a href="{{route('front.product.show',$product->translate->link_rewrite)}}"
                               class="block bg-transparent z-1 text-decoration-none text-center overflow-hidden border-0 relative h-[250px]">
                                <div
                                    class="flex items-center justify-center absolute overflow-hidden left-0 right-0 top-0 bottom-0">
                                    <img width="250px" height="250px"
                                         class="mh-100 text-center mw-100 h-auto w-auto text-sm"
                                         src="{{$product->mainImage->getFullUrl()}}" alt="product image"/>
                                </div>
                            </a>
                        </div>
                        <div class="text-center m-[4px] relative">
                            <div class="text-center m-[4px] relative">
                                <div class="inline-block">
                                    <div class="text-xs">Артикул: 30023</div>
                                    {{$product['id']}}
                                </div>
                                <div class="inline-block ml-[5px]">
                                    <div>stars</div>
                                </div>
                                <div class="inline-block text-xs">
                                    comments
                                </div>
                            </div>
                            <div
                                class="overflow-hidden h-[74px] mh-3 pb-1 my-[6px] text-md text-ellipsis center leading-5">
                                <a href="#">
                                    {{$product['title']}}
                                </a>
                            </div>
                            <div class="flex items-center justify-center mt-[10px]">
                                <span class="text-md font-bold text-gray-900 align-self-center leading-5 mr-[10px]">
                                    {{priceFormat($product['price'])}}
                                </span>
                                <span
                                    class="text-md font-normal text-gray-900 align-self-center line-through leading-5 mr-[10px]">
                                    {{priceFormat($product['price'])}}
                                </span>
                            </div>
                            <div class="mt-2">
                                <button wire:click="addToCart({{$product['id']}})" type="button"
                                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-1.5 mr-1 mb-1 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                    Add to cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{$hits->links()}}
            </div>
        </div>
    </div>
</div>
