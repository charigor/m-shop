@extends('layouts.main')

@section('content')

    <livewire:filter-products :model="$brand"/>

    <div>
        {!!$brand->description!!}
    </div>
@endsection
