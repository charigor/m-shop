@extends('layouts.main')

@section('content')
    @if($product)
            <div class="flex">
                @foreach($product->attributes as $key => $attr)
                <div class="mb-5 flex-col w-2/3 {{$attr->default ? 'flex' : 'hidden'}}">
                   <x-swiper-slider :key="$key" :product="$attr"></x-swiper-slider>
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700" x-data="{ activeTab:  0 }">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" data-tabs-toggle="#tabFeatures-{{$key}}"
                            role="tablist">
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 0"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 0 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="description-{{$key}}-tab" data-tabs-target="#description-{{$key}}" type="button" role="tab"
                                        aria-controls="description-{{$key}}"
                                        aria-selected="true">{{trans('page/product.fields.description')}}</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 1"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 1 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="features-{{$key}}-tab" data-tabs-target="#features-{{$key}}" type="button" role="tab"
                                        aria-controls="#featires-{{$key}}">{{trans('page/feature.title_plural')}}</button>
                            </li>
                        </ul>
                        <div id="tabFeatures-{{$key}}">
                            <div :class="{ '!block': activeTab === 0 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 0"
                                 class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="description-{{$key}}" role="tabpanel"
                                 aria-labelledby="description-{{$key}}-tab">
                                {!! $product->translate->description !!}
                            </div>
                            <div :class="{ '!block': activeTab === 1 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 1"
                                 class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="features-{{$key}}" role="tabpanel"
                                 aria-labelledby="features-{{$key}}-tab">
                                <div class="relative">
                                    <table class="text-sm text-left text-gray-500 dark:text-gray-400">
                                        <tbody>
                                        <tr class="dark:bg-gray-900 dark:border-gray-700">
                                            <td class="px-6 py-2 font-bold"> {{trans('page/brand.title')}}</td>
                                            <td class="px-6 text-xs py-2"><a class="underline hover:text-cyan-600" href="{{route('front.brand.show',$product->brand->slug)}}">{{$product->brand->name}}</a></td>
                                        </tr>
                                        @foreach($product->features as $feature)
                                            <tr class="dark:bg-gray-900 dark:border-gray-700">
                                                <td class="px-6 py-2 font-bold">{{$feature->feature->translate->name}}</td>
                                                <td class="px-6 text-xs py-2">{{$feature->translate->value}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-5 py-5 flex-col {{$attr->default ? 'flex' : 'hidden'}}">
                    <h2 class="text-xl mb-2 text-gray-600">{{$product->translate->name}}</h2>
                    <div class="mb-2">
                        <p class="text-sm"><span>Артикул:</span> {{$product->reference}} </p>
                    </div>
                    <div class="text-3xl mb-2">{{priceFormat($attr->price,2)}} грн</div>

                    <div>
                        <button class="focus:outline-none d-block text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Купити
                        </button>
                    </div>
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700" x-data="{ activeTab:  0 }">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" data-tabs-toggle="#tabInfo-{{$key}}"
                            role="tablist">
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 0"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 0 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="info-{{$key}}-tab" data-tabs-target="#info-{{$key}}" type="button" role="tab"
                                        aria-controls="#info-{{$key}}"
                                        aria-selected="true">Info</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 1"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 1 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="delivery-{{$key}}-tab" data-tabs-target="#delivery-{{$key}}" type="button" role="tab"
                                        aria-controls="#delivery-{{$key}}">Delivery</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 2"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 2 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="payment-{{$key}}-tab" data-tabs-target="#payment-{{$key}}" type="button" role="tab"
                                        aria-controls="#payment-{{$key}}">Payment</button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button @click="activeTab = 3"
                                        :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 3 }"
                                        class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                        id="payback-{{$key}}-tab" data-tabs-target="#payback-{{$key}}" type="button" role="tab"
                                        aria-controls="#payback-{{$key}}">Payback</button>
                            </li>
                        </ul>
                        <div id="tabInfo-{{$key}}">
                            <div :class="{ '!block': activeTab === 0 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 0"
                                 class="hidden p-4 rounded-lg  dark:bg-gray-800" id="info-{{$key}}" role="tabpanel"
                                 aria-labelledby="info-{{$key}}-tab">
                                Інформація

                            </div>
                            <div :class="{ '!block': activeTab === 1 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 1"
                                 class="hidden p-4 rounded-lg dark:bg-gray-800" id="delivery-{{$key}}" role="tabpanel"
                                 aria-labelledby="delivery-{{$key}}-tab">
                               Доставка
                            </div>
                            <div :class="{ '!block': activeTab === 2 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 2"
                                 class="hidden p-4 rounded-lg dark:bg-gray-800" id="payment-{{$key}}" role="tabpanel"
                                 aria-labelledby="payment-{{$key}}-tab">
                                Оплата
                            </div>
                            <div :class="{ '!block': activeTab === 3 }"
                                 x-show.transition.in.opacity.duration.600="activeTab === 3"
                                 class="hidden p-4 rounded-lg dark:bg-gray-800" id="payback-{{$key}}" role="tabpanel"
                                 aria-labelledby="payback-{{$key}}-tab">
                                Ви можете відмовитися від доставленого товару в разі:

                                якщо він не відповідає тому артикулу, який ви замовляли, не працює або зіпсований;
                                якщо він не задовольнив вас за формою, габаритами, фасоном, кольором, розміром або з інших причин не може бути ним використаний за призначенням.
                                Ви можете відмовитися від товару безпосередньо в момент отримання і повернути його кур’єру. Виключення складає розпакований товар, що був в обрешітці (наприклад, акваріум, інші крихкі предмети). Кур’єр не зможе його забрати, бо він потребує ретельного пакування при відправленні.

                                У такому випадку ви маєте самостійно відправити товар з відділення та обов'язково прослідкувати, щоб упаковка товару була така ж, як і при відправці (обрешітка, додаткова упаковка). В цьому випадку витрати за пакування та зворотну доставку сплачуємо ми.

                                Повернення товару здійснюється відповідно до Закону України «Про захист прав споживачів».

                                Згідно постанови Кабінету міністрів №172 від 19 березня 1994 року про реалізацію окремих положень закону України "Про захист прав споживачів" не підлягають поверненню:

                                продовольчі товари;
                                товари медичного призначення: лікарські препарати та прилади для лікування тварин, медичний одяг;
                                попони, бандажі та тп.
                                предмети гігієни;
                                м'які або надувні іграшки;
                                товари для цуценят і кошенят (пелюшки, соски, пляшечки для годування, поїльника і т.д.);
                                парфумерно-косметичні та товари в аерозольній упаковці;
                                рушники, покривала, лежаки, будиночки;
                                зубні щітки, гребінці, щітки, інструменти для грумінгу;
                                панчішно-шкарпеткові вироби.
                                За умовами магазину MasterZoo обміну та поверненню не підлягають подарункові сертифікати.

                                Обов'язково перевіряйте товар відразу на відділенні Нова Пошта або при доставці кур'єром.

                                Претензії на предмет бою і пошкодження товару не приймаються після підписання документа про отримання.
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
    @endif

@endsection
