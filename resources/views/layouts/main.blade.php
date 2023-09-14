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
    @section('header')
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
    @endsection
    @yield('header')
    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>
</div>


@livewireScripts
<script>
    var
        getNextArr = function(prevArr){ // функция для построения следующего массива из предыдущего
            var
                newLen =  prevArr.length + 9, // длинна следующего массива будет больше на 9
                arr = []; // заготовка результата

            for(var i=0; i<newLen; i++){
                var q = 0; // заготовка нового значения
                for(j=0; j<10; j++) // берем 10 нужных значений
                    if(prevArr[i-j]) // ...если они существуют в предыдущем массиве
                        q+=prevArr[i-j]; // добавляем
                arr[i] = q; // или arr.push(q);
            }

            return arr;
        },
        luckyTickets = function(num){ // собственно сам  счетчик
            var
                arr = [], // первый массив
                result = 0; // то, что мы вернем
            for(i=0;i<10;i++) arr.push(1); // впихиваем в первый массив 10 единиц

            for(i=0;i<(num/2-1);i++) // нужное количество раз

                arr = getNextArr(arr); // строим следующие массивы
            // console.log(arr);
            arr.forEach(function(v){
                console.log(Math.pow(v,2));
                result+=Math.pow(v,2);
            }); // сводим квадраты значений в получившемся массиве
            return result;
        };
    console.log( luckyTickets(6) ); // 8.014950093120178e+297 **
</script>
</body>
</html>


