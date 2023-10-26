<div class="mb-4 border-t border-gray-200 dark:border-gray-700" x-data="{ activeTab:  0 }">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" data-tabs-toggle="#tabFeatures"
        role="tablist">
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 0"
                    :class="{ 'text-cyan-700 dark:text-gray-300': activeTab === 0 }"
                    class="inline-block p-4  rounded-t-lg hover:text-cyan-500 hover:border-gray-300 dark:hover:text-gray-300"
                    id="description-tab" data-tabs-target="#description" type="button" role="tab"
                    aria-controls="description"
                    aria-selected="true">{{trans('page/product.fields.description')}}</button>
        </li>
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 1"
                    :class="{ 'text-cyan-700  dark:text-gray-300': activeTab === 1 }"
                    class="inline-block p-4  rounded-t-lg hover:text-cyan-500 hover:border-gray-300 dark:hover:text-gray-300"
                    id="features-tab" data-tabs-target="#features" type="button" role="tab"
                    aria-controls="#featires">{{trans('page/feature.title_plural')}}</button>
        </li>
    </ul>
    <div id="tabFeatures">
        <div :class="{ '!block': activeTab === 0 }"
             x-show.transition.in.opacity.duration.600="activeTab === 0"
             class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="description" role="tabpanel"
             aria-labelledby="description-tab">
            {!! $product->translate->description !!}
        </div>
        <div :class="{ '!block': activeTab === 1 }"
             x-show.transition.in.opacity.duration.600="activeTab === 1"
             class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="features" role="tabpanel"
             aria-labelledby="features-tab">
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
