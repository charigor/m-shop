@extends('layouts.main')

@section('content')

    <livewire:filter-products :model="$category"/>
    <div>
        {!!$category->description!!}
    </div>
@endsection
