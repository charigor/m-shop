@foreach($category as $child)
    <li class="mb-[5px]">
        <a href="{{route('front.category.show',['slug' => $child->translate->link_rewrite])}}"
           class="flex  items-center  p-[7px] hover:text-blue-500 border-0 text-sm leading-4 text-blue-600 flex">
{{--                <div class="w-[50px] h-[50px]">--}}
{{--                    <img class="mh-100 text-center mw-100 h-auto w-auto text-sm"--}}
{{--                         src="{{$child->getFirstMediaUrl('cover_image')}}"--}}
{{--                         alt="{{$child->translate->title}}">--}}
{{--                </div>--}}
            @if(isset($title))
                <b
                    class="text-black font-semibold">{{$child->translate->title}}</b>
            @else
                <span
                    class="text-cyan-600">{{$child->translate->title}}</span>
            @endif
        </a>
            <ul class="ml-3">
                @if($child->children->count())
                    @php $category = $child->children;@endphp
                    <x-category :category="$category" />
                @endif
            </ul>
    </li>

@endforeach




