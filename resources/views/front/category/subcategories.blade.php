@extends('layouts.main')

@section('content')
    <div class="mb-4">
        <h1 class="font-semibold my-2 text-2xl">{{$category->title}}</h1>
    </div>
    <div class="flex flex-wrap mb-5 ">
        @foreach($category->children as $cat)
            <div class="h-auto pb-[25px] xs:w-[100%] sm:w-[33.33%] md:w-[25%] lg:w-[20%] xl:w-[16.66667%] group hover:shadow-2xl transition-all">
                <div>
                    <div class="pb-[10px] relative">
                        <div class="z-10">
                            <a class="block border-0" href="{{route('front.category.show',['slug' => $cat->link_rewrite])}}">
                                <div class="h-[200px] block relative z-1 overflow-hidden border-0 text-center">
                                    <div class="flex items-center justify-center overflow-hidden absolute left-0 right-0 bottom-0 top-0">
                                        <img class="w-[180px] mh-100 text-center mw-100 h-auto w-auto text-sm" src="{{$cat->getFirstMediaUrl('cover_image')}}" alt="{{ $cat->title}}">
                                    </div>
                                </div>
                                <div class="px-3 relative">
                                    <div class="text-md text-center mb-[6px] leading-4 font-normal text-ellipsis mh-[3.6em] text-main-dark">
                                        <span class="hover:text-cyan-600 font-semibold transition-all">{{$cat->title}}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    <div>
        {!!$category->description!!}
    </div>

@endsection
