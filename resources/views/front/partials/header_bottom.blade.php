<div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light border-t-2 border-b-2">
    <div class="bg-light-100 text-main-dark">
        <div class="container mx-auto ">
            <div class="columns-1 relative">
                <ul class="flex justify-center category_menu">
                     @foreach($categories as $category)
                            @if(is_null($category->parent_id))
                                <li class="px-2 px-1">
                                    <div>
                                        <a href="{{route('front.category.show',['slug' => $category->translate->link_rewrite])}}"
                                           class=" flex font-semibold items-center p-[7px] hover:text-cyan-500 hover:border-cyan-500 border-0 text-md leading-4 text-cyan-600 {{request()->segment(count(request()->segments())) === $category->translate->link_rewrite ? 'text-yellow-600 border-b-2 border-yellow-600': '' }}">{{$category->translate->title}}</a>
                                    </div>
                                    @if($category->children->count())
                                        @php
                                            $category = $category->children;
                                        @endphp
                                        <div style="transform:translate(-50%);" class="absolute w-[800px] left-[50%] z-50 bg-white overflow-hidden rounded invisible submenu shadow-xl">
                                            <ul class="flex flex-wrap">
                                                 <x-category :category="$category" :title="true"/>
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach

                        <li class="px-2 px-1">
                            <div class="relative">
                                <a href="{{route('front.brand.index')}}"
                                   class=" flex font-semibold items-center justify-center p-[7px] hover:text-cyan-500 hover:border-cyan-500 border-0 text-md leading-4 text-cyan-600 {{\Request::route()->getName() === 'front.brand.index' ? 'text-yellow-600 border-b-2 border-yellow-600': '' }}">{{trans('page/brand.title_plural')}}</a>
                            </div>
                        </li>

                </ul>
            </div>
        </div>
    </div>
</div>
