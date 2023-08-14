<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="/front.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet" />


    <!-- Focus plugin -->
    <script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{--    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">--}}
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/datepicker.min.js"></script>--}}
    @livewire('livewire-ui-modal')
    @livewireStyles
</head>
<body class="font-sans antialiased">

<div>
    <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
        @include('front.partials.top-panel')
    </div>
    <div class="bg-main-light text-main-dark dark:bg-main-dark dark:text-main-light">
        @include('front.partials.navigation')
    </div>

        @include('front.partials.header_middle')
    </div>

        @include('front.partials.header_bottom')
    </div>
    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>
</div>


@livewireScripts

</body>
</html>


