
    <div  class="top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Cart
                    </h3>
                    <button type="button"  wire:click.prevent="$emit('closeModal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6 w-100">


                       <table class="w-[100%] border-collapse">
                           <thead>
                                <tr>
                                    <th class="pb-2"></th>
                                    <th class="pb-2"></th>
                                    <th class="text-sm px-2 pb-2">Кількість</th>
                                    <th class="text-right text-sm pr-4 pb-2">Вартість</th>
                                </tr>
                           </thead>
                           <tbody class="bg-white w-[100%] my-4">
                               @foreach($cartProducts as $product)
                                    <tr class="border">
                                        <td class="pl-4 py-2 text-left">
                                            <div class="w-100 h-[80px] inline-block">
                                                <img class="max-w-[80px] max-h-[80px] w-auto h-auto" src="{{$product['image']}}" alt="{{$product['name']}}">
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="min-w-[150px] pr-5">
                                                <h5 class="mb-2 text-md font-semibold tracking-tight text-gray-900 dark:text-white">{{$product['name']}}</h5>
                                                <div>{{priceFormat($product['price'])}} грн</div>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            <div class="flex border">
                                                <button type="button" class="{{$product['quantity'] == 1 ? 'pointer-events-none': ''}} ml-2 mr-4" wire:click.prevent="$emit('decrease',{{$product['product_id']}})">
                                                    <i class="mdi mdi-minus"></i>
                                                </button>
                                                <input type="number" class="w-[50px] border-0" min="1" value="{{$product['quantity']}}">
                                                <button type="button" class="mr-2" wire:click.prevent="$emit('increase',{{$product['product_id']}})">
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
                                <tr>
                                    <td colspan="2" class="pt-2 text-left">
                                        <button  wire:click.prevent="$emit('closeModal')" type="button" class=" text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"><i class="mdi mdi-arrow-left mr-2"></i>Повернутися до покупок</button>
                                    </td>
                                    <td colspan="2" class="pt-2 text-right">

                                        <button  type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Оформити замовлення</button>
                                    </td>
                                </tr>
                            </tfoot>
                       </table>

                </div>
            </div>
        </div>
    </div>

