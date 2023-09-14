<div class="mb-5 flex">
    <div class="w-[250px] p-2">
        <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
            <div class="bg-light-100 text-main-dark" >
                @foreach($allFilters as $key => $item)

                    @if($key === 'price' && count($item))
                        <br>
                        <span class="text-xs bg-white-100 p-2 border whitespace-nowrap">Ціна:</span>
                        <span class="text-xs bg-green-100 p-2 border whitespace-nowrap">{{$item[0] }} - {{$item[1]}} </span><span class="rounded-circle p-2 cursor-pointer text-xs border bg-green-100">x</span>
                    @endif

                    @if($key === 'brand' && count($item))
                        <br>
                            <span class="text-xs bg-white-100 p-2 border whitespace-nowrap">Бренд:</span>
                        @foreach($item as $elem)
                            <span class="text-xs bg-green-100 p-2 border whitespace-nowrap">{{$elem->name}} </span><span class="rounded-circle p-2 cursor-pointer text-xs border bg-green-100">x</span>
                        @endforeach
                    @endif
                    @if($key === 'feature')
                            @foreach($item as  $key => $elem)
                            <br>
                                <span class="text-xs bg-white-100 p-2 border whitespace-nowrap">{{$key}}:</span>
                                @foreach($elem as $e)

                                <span class="text-xs bg-green-100 p-2 border whitespace-nowrap">{{$e->value_name}} </span><span class="rounded-circle p-2 cursor-pointer text-xs border bg-green-100">x</span>
                                @endforeach
                            @endforeach
                    @endif
                @endforeach
            </div>
        </div>
        <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
            <div class="bg-light-100 text-main-dark" >
                <div class="font-semibold rounded-lg text-md px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ціна</div>
                                <!-- Dropdown menu -->
                <div  class="z-10  transition-all bg-white ml-3" :class="!open ? 'invisible' : 'visible'" x-show.transition="open">
                    <div  class="relative flex items-center">
                        <input id="item-price-min"  wire:model="price.0" value="{{$price[0]}}"
                               type="text"
                               class="w-[45%] text-xs text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <span class="px-2">-</span>
                        <input id="item-price-max" wire:model="price.1" value="{{$price[1]}}"
                               type="text"
                               class="w-[45%] text-xs  text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <button wire:click="updatePrice"   class="font-semibold  border text-xs rounded-lg text-md px-2.5 ml-2  py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">ok </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
            <div class="bg-light-100 text-main-dark" x-data="{ open: true }">
                <button @click.prevent="open = !open" id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
                        class="font-semibold rounded-lg text-md px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                    <svg x-bind:class="! open ? '' : 'rotate-90'" class="w-2.5 h-2.5 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    {{trans('page/brand.title_plural')}}
                </button>
                <!-- Dropdown menu -->
                <div  class="z-10  transition-all bg-white ml-3" :class="!open ? 'invisible' : 'visible'" x-show.transition="open">
                    <ul class="text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                        @foreach($brands as $brand)
                                <li class="px-1 ml-4">
                                    <div class="relative flex items-center">
                                        <input id="checkbox-item-{{$brand->id}}" {{!$brand->products_count ? 'disabled': '' }}   wire:model.debounce.250ms="filter.brand"
                                               type="checkbox" value="{{$brand->id}}"
                                               class="w-4 h-4  disabled:opacity-25 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label for="checkbox-item-{{$brand->id}}" {{!$brand->products_count ? 'disabled': '' }}
                                        class="disabled:opacity-25 flex font-semibold items-center p-[7px] hover:text-cyan-80 border-0 text-md leading-3 text-black">{{$brand->name}}   <span class="ml-2 mb-[2px] text-xs text-cyan-600">{{$brand->products_count}}</span></label>

                                    </div>
                                </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        @foreach($features as $feature)
            <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
                <div class="bg-light-100 text-main-dark" x-data="{ open: true }">
                    <button @click.prevent="open = !open" id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
                            class="font-semibold rounded-lg text-md px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            type="button">
                        <svg x-bind:class="! open ? '' : 'rotate-90'" class="w-2.5 h-2.5 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        {{$feature->translate->name}}
                    </button>
                    <!-- Dropdown menu -->
                    <div  class="z-10  transition-all bg-white ml-3" :class="!open ? 'invisible' : 'visible'" x-show.transition="open">
                        <ul class="text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">

                            @foreach($feature->featureValue as $value)

                                    <li class="px-1 ml-4">
                                        <div class="relative flex items-center">
                                            <input id="checkbox-item-{{$value->id}}"  {{!$value->feature_values_product_count ? 'disabled': '' }}  wire:model.debounce.250ms="filter.feature"
                                                   type="checkbox" value="{{$value->id}}"
                                                   class="disabled:opacity-25 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                            <label for="checkbox-item-{{$value->id}}" {{!$value->feature_values_product_count ? 'disabled': '' }}
                                                   class="disabled:opacity-25 flex font-semibold items-center p-[7px] hover:text-cyan-80 border-0 text-md leading-3 text-black">{{$value->translate->value}} <span class="ml-2 mb-[2px] text-xs text-cyan-600">{{$value->feature_values_product_count}}</span></label>

                                        </div>
                                    </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
        </div>

    <div style="width: calc(100% - 250px)" class="mt-4">
        <div class="flex items-center justify-end">
            <div class="flex  items-center">
                <span class="text-sm">Сортування:</span>
                <div class="whitespace-nowrap overflow-hidden flex border-2 border-color-gray-200 rounded-lg ml-4 text-sm">
                    <span class="relative px-2 py-1 {{$sortBy === 'cheap' ? 'bg-purple-200' : ''}}"><label class="cursor-pointer"><input wire:model="sortBy"  class="hidden" value="cheap" type="radio">Спочатку дешевше</label></span>
                    <span class="relative px-2 py-1 {{$sortBy === 'expensive' ? 'bg-purple-200' : ''}}"><label class="cursor-pointer "><input wire:model="sortBy"  class="hidden" value="expensive" type="radio">Спочатку дорожче</label></span>
                    <span class="relative px-2 py-1 {{$sortBy === 'name' ? 'bg-purple-200' : ''}}"><label class="cursor-pointer"><input wire:model="sortBy"  class="hidden" value="name" type="radio">За назвою</label></span>
                </div>
            </div>
        </div>
        <div class="flex container">
            @foreach($products as $product)
                <div class="z-1 h-100 sm: md:w-[25%] lg:w-[20%] xl:w-[16.66667%] relative">
                    <div class="m-[4px] relative">
                        <a href="#"
                           class="block bg-transparent z-1 text-decoration-none text-center overflow-hidden border-0 relative h-[250px]">
                            <div
                                class="flex items-center justify-center absolute overflow-hidden left-0 right-0 top-0 bottom-0">
                                <img width="250px" height="250px"
                                     class="mh-100 text-center mw-100 h-auto w-auto text-sm"
                                     src="{{$product->getFirstMediaUrl('image')}}" alt="product image"/>
                            </div>
                        </a>
                    </div>
                    <div class="text-center m-[4px] relative">
                        <div class="text-center m-[4px] relative">
                            <div class="inline-block">
                                <div class="text-xs">Артикул: 30023</div>
                            </div>
                            <div class="inline-block ml-[5px]">
                                <div>stars</div>
                            </div>
                            <div class="inline-block text-xs">
                                comments
                            </div>
                        </div>
                        <div class="overflow-hidden h-[74px] mh-3 pb-1 my-[6px] text-md text-ellipsis center leading-5">
                            <a href="#">
                                {{$product->name}}
                            </a>
                        </div>
                        <div class="flex items-center justify-center mt-[10px]">
                            <span class="text-md font-bold text-gray-900 align-self-center leading-5 mr-[10px]">
                                {{priceFormat($product->price)}}
                            </span>
                            <span class="text-md font-normal text-gray-900 align-self-center line-through leading-5 mr-[10px]">
                                {{priceFormat($product->price)}}
                            </span>
                        </div>
                        <div class="mt-2">
                            <button wire:click="addToCart({{$product->id}})" type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-1.5 mr-1 mb-1 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Add to cart</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
