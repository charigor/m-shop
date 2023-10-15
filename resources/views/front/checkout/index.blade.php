@extends('layouts.main')
@section('header')
    <div>
        <div class="bg-cyan-600 text-main-light dark:bg-main-dark dark:text-main-light">
            <div class="bg-light-100">
                <div class="container mx-auto">
                    <div class="items-center flex">
                        <a href="/" class="items-start">
                            <h1 class="font-bold text-2xl text-yellow-200">M-SHOP</h1>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="flex">
        <div class="w-[calc(100%-520px)] border p-5">
            <livewire:checkout/>
        </div>
        <div class="w-auto w-[515px] border p-5 ml-[20px]">
            <livewire:checkout-aside/>
        </div>
    </div>
@endsection
