@extends('layouts.main')

@section('content')
    @if($product)
      <livewire:product-card :product="$product"/>
    @endif
@endsection

