<div class="mb-5 flex">
    <div class="w-[250px] p-2">
            @include('front.partials.catalog_aside')

        <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
            <div class="bg-light-100 text-main-dark" x-data="{ open: true }">
                <button @click.prevent="open = !open" id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
                        class="font-semibold rounded-lg text-md px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                    <svg x-bind:class="! open ? '' : 'rotate-90'" class="w-2.5 h-2.5 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    Розділ
                </button>
                <!-- Dropdown menu -->
                <div  class="z-10  transition-all bg-white ml-3" :class="!open ? 'invisible' : 'visible'" x-show.transition="open">
                    <ul class="text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                        @foreach($sections as $category)
                            @if(!is_null($category->parent_id) && $category->products_count)
                                <li class="px-1 ml-4">
                                    <div class="relative flex items-center">
                                        <input id="checkbox-item-{{$category->id}}" wire:model.debounce.250ms="categories_id"
                                               type="checkbox" value="{{$category->id}}"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label for="checkbox-item-{{$category->id}}"
                                               class="flex font-semibold items-center p-[7px] hover:text-cyan-80 border-0 text-md leading-3 text-black">{{$category->title}}   <span class="ml-2 mb-[2px] text-xs text-cyan-600">{{$category->products_count}}</span></label>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
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
                        <div class="flex items-center justify-center mt-[10px] ">
                    <span class="text-md font-bold text-gray-900 align-self-center leading-5 mr-[10px]">
                        {{$product->price}}</span>
                            <span
                                class="text-md font-normal text-gray-900 align-self-center line-through leading-5 mr-[10px]">
                        {{$product->price}}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
