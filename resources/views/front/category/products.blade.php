@extends('layouts.main')

@section('content')
    <livewire:filter-products :category_id="$category->id"/>
    <div>
        {!!$category->description!!}
    </div>
@endsection
