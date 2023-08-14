@extends('layouts.main')

@section('content')

     <livewire:filter-brand-products :brands_id="$brand->id"/>

    <div>
        {!!$brand->description!!}
    </div>
@endsection
