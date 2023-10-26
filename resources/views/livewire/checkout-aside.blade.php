<div class="w-[390px]">
@if($cartProducts->count())
    <table class="w-[100%] border-collapse">
        <tbody class="bg-white my-4">
        @foreach($cartProducts as $product)
            <tr class="border">
                <td class="pl-4 py-2 text-left">
                    <div class="w-100 h-[80px] inline-block">
                        <img class="max-w-[80px] max-h-[80px] w-auto h-auto" src="{{$product['image']}}"
                             alt="{{$product['name']}}">
                    </div>
                </td>
                <td class="p-2">
                    <div class="min-w-[150px] pr-5">
                        <h5 class="mb-2 text-md font-semibold tracking-tight text-gray-900 dark:text-white">{{$product['name']}}</h5>
                        <div>{{priceFormat($product['price'])}} грн</div>
                    </div>
                </td>
                <td class="p-2">
                    <div class="flex border justify-between">
                        <button type="button"
                                class="{{$product['quantity'] == 1 ? 'pointer-events-none': ''}} ml-2 mr-4"
                                wire:click.debounce.500ms.prevent="$emit('decrease',{{$product['product_id']}},{{$product['attribute_id']}})">
                            <i class="mdi mdi-minus"></i>
                        </button>
                        <input type="number" class="w-[50px] border-0" min="1" readonly
                               value="{{$product['quantity']}}">
                        <button type="button" class="mr-2"
                                wire:click.debounce.500ms.prevent="$emit('increase',{{$product['product_id']}},{{$product['attribute_id']}})">
                            <i class="mdi mdi-plus"></i>
                        </button>
                    </div>
                </td>
                <td class="text-right pr-4 py-2">
                    <span>{{priceFormat(($product['price'] * $product['quantity']))}} грн</span>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4">
                <div class="text-right my-4">Всього: {{$summary}} грн</div>
            </td>
        </tr>
        </tfoot>
    </table>
@endif
</div>
