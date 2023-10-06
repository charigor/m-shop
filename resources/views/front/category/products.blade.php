@extends('layouts.main')

@section('content')

    <livewire:filter-products :category_slug="$category->translate->link_rewrite"/>

    <div>
        {!!$category->description!!}
    </div>
@endsection
