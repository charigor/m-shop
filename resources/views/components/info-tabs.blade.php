<div class="mb-4  border-t border-gray-200 dark:border-gray-700" x-data="{ activeTab:  0 }">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" data-tabs-toggle="#tabInfo"
        role="tablist">
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 0"
                    :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 0 }"
                    class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="info-tab" data-tabs-target="#info" type="button" role="tab"
                    aria-controls="info"
                    aria-selected="true">Info</button>
        </li>
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 1"
                    :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 1 }"
                    class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="delivery-tab" data-tabs-target="#delivery" type="button" role="tab"
                    aria-controls="#delivery">Delivery</button>
        </li>
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 2"
                    :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 2 }"
                    class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="payment-tab" data-tabs-target="#payment" type="button" role="tab"
                    aria-controls="#payment">Payment</button>
        </li>
        <li class="mr-2" role="presentation">
            <button @click="activeTab = 3"
                    :class="{ 'text-gray-800 text-cyan-500 dark:text-gray-300': activeTab === 3 }"
                    class="inline-block p-4  rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                    id="payback-tab" data-tabs-target="#payback" type="button" role="tab"
                    aria-controls="#payback">Payback</button>
        </li>
    </ul>
    <div id="tabInfo">
        <div :class="{ '!block': activeTab === 0 }"
             x-show.transition.in.opacity.duration.600="activeTab === 0"
             class="hidden p-4 rounded-lg  dark:bg-gray-800" id="info" role="tabpanel"
             aria-labelledby="info-tab">
            Інформація

        </div>
        <div :class="{ '!block': activeTab === 1 }"
             x-show.transition.in.opacity.duration.600="activeTab === 1"
             class="hidden p-4 rounded-lg dark:bg-gray-800" id="delivery" role="tabpanel"
             aria-labelledby="delivery-tab">
            Доставка
        </div>
        <div :class="{ '!block': activeTab === 2 }"
             x-show.transition.in.opacity.duration.600="activeTab === 2"
             class="hidden p-4 rounded-lg dark:bg-gray-800" id="payment" role="tabpanel"
             aria-labelledby="payment-tab">
            Оплата
        </div>
        <div :class="{ '!block': activeTab === 3 }"
             x-show.transition.in.opacity.duration.600="activeTab === 3"
             class="hidden p-4 rounded-lg dark:bg-gray-800" id="payback" role="tabpanel"
             aria-labelledby="payback-tab">
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
