@extends('layouts.main')
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('front.main') }}" method="get" class="pb-4">
                        <div class="form-group">
                            <input
                                type="text"
                                name="q"
                                class="form-control"
                                placeholder="Search..."
                                value="{{ request('q') }}"
                            />
                        </div>
                    </form>
                    @if (request()->has('q'))
                        <p class="text-sm">Using search: <strong>"{{ request('q') }}"</strong>. <a class="border-b border-indigo-800 text-indigo-800" href="{{ route('front.main') }}">Clear filters</a></p>
                    @endif
                    <div class="mt-8 space-y-8">
                        @forelse ($products as $product)
                            @if(request()->has('q'))
                            <article class="space-y-1">
                                <h2 class="font-semibold text-2xl">
                                    {!! str_replace(request('q'), "<span class='text-yellow-300'>" . request('q'). "</span>",  $product->name)!!}
                                </h2>
                                <div>
                                    <p>{{$product->description}}</p>
                                </div>
                            </article>
                            @endif
                        @empty
                            <p>No articles found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
