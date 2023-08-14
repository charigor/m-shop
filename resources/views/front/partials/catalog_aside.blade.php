<div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
    <div class="bg-light-100 text-main-dark">
        <ul class="flex-column  category_menu">
            @foreach($categories as $category)
                @if(is_null($category->parent_id))
                    <li class="px-2 px-1">
                        <div class="relative">
                            <a href="{{route('front.category.show',['slug' => $category->link_rewrite])}}"
                               class=" flex font-semibold items-center p-[7px] hover:text-cyan-80 border-0 text-md leading-3 text-black {{request()->segment(count(request()->segments())) === $category->link_rewrite ? 'text-yellow-600 border-b-2 border-yellow-600': '' }}">{{$category->title}}</a>
                        </div>
                    </li>
                @endif
            @endforeach
            <li class="px-2 px-1">
                <div class="relative">
                    <a href="{{route('front.brand.index')}}"
                       class=" flex font-semibold items-center p-[7px] hover:text-cyan-800  border-0 text-md leading-3 text-black {{\Request::route()->getName() === 'front.brand.index' ? 'text-yellow-600 border-b-2 border-yellow-600': '' }}">{{trans('page/brand.title_plural')}}</a>
                </div>
            </li>
        </ul>
    </div>
</div>
