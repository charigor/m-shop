<div class="bg-green-500 text-main-light">
    <div class="container mx-auto">
        <div class="columns-2">
            <nav class="j-site-menu text-sm py-2 ">
                <span><a class="p-2" href="/ua/zoomarketi/">Магазини MasterZoo</a></span>
                <span ><a class="p-2" href="/ua/oplata-i-dostavka/">Оплата та доставка</a></span>
                <span ><a class="p-2" href="/ua/keshbek/">Кешбек</a></span>
            </nav>
            <div class="flex">
                <ul class="flex ml-auto">
                    @foreach($shopLanguages as $item)

                        <li class="p-2 px-1"><a class="{{$item == app()->getLocale() ? 'bg-cyan-600 p-2' : 'p-2'}}" href="{{route('front.language.locale',$item)}}">{{$item}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
