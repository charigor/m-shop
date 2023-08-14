<div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light border-t-2 border-b-2">
    <div class="bg-light-100 text-main-dark">
        <div class="container mx-auto">
            <div class="columns-1">
                <ul class="flex justify-center category_menu">
                    @foreach($categories as $category)

                        @if(is_null($category->parent_id))

                            <li class="px-2 px-1">
                                <div class="relative">
                                    <a href="{{route('front.category.show',['slug' => $category->link_rewrite])}}"
                                       class=" flex font-semibold items-center justify-center p-[7px] hover:text-cyan-500 hover:border-cyan-500 border-0 text-md leading-4 text-cyan-600 {{request()->segment(count(request()->segments())) === $category->link_rewrite ? 'text-yellow-600 border-b-2 border-yellow-600': '' }}">{{$category->title}}</a>
                                </div>

                                @if($category->children->count())

                                    <div
                                        class="absolute  z-50 bg-white  overflow-hidden rounded invisible submenu shadow-xl">
                                        <ul style="{{'width:'.($category->children->count() * 270)."px"}}">
                                            @foreach($category->children as $child)
                                                <li class="mb-[10px]">
                                                    <a href="{{route('front.category.show',['slug' => $child->link_rewrite])}}"
                                                       class="flex font-semibold items-center justify-center p-[7px] hover:text-blue-500 border-0 text-md leading-4 text-blue-600 flex">
                                                        <div class="w-[50px] h-[50px]">
                                                            <img class="mh-100 text-center mw-100 h-auto w-auto text-sm"
                                                                 src="{{$child->getFirstMediaUrl('cover_image')}}"
                                                                 alt="{{$child->title}}">
                                                        </div>
                                                        <div class="w-[100px]">
                                                            <span
                                                                class="text-cyan-600">{{$child->title}}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
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
