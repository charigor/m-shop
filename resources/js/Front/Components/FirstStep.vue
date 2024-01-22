<template>
    <div>
        <h2>Оберіть населений пункт</h2>
            <div>
                <select class="w-[1px] h-0 invisible" name="city" id="city" v-model="city"></select>
                <div   @click="cityActive = !cityActive" class="bg-gray-50 py-5 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <span :class="{'pointer-events-none' : cityActive}">{{city.Present}}</span>
                </div>
            </div>
        <div class="relative" v-if="cityActive"  >
            <input  class="block w-full p-4  text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" v-model="searchTerm" @input="onSearch" placeholder="Search cities...">
            <ul  class="border border-gray-300 mt-3 p-4 h-[300px] overflow-y-auto" @scroll="onScroll" v-if="cities.length && cityActive">
                <li v-for="city in cities" :key="city.Ref" @click="select(city)" class="whitespace-nowrap cursor-pointer py-1">{{ city.Present }}</li>
            </ul>
            <span v-if="!cities.length">Введіть будь ласка 1 або більше літер</span>
            <span v-if="loading  && cities.length">Підгрузка додаткових міст</span>
        </div>

<!--        <button @click="loadMore" :disabled="loading">Load More</button>-->
    </div>
</template>

<script>
import debounce from "lodash.debounce";

export default {
    data() {
        return {
            searchTerm: '',
            cities: [],
            page: 1,
            loading: false,
            city: '',
            cityActive: false
        };
    },
    methods: {
        onSearch: debounce(async function () {
            this.page = 1;
            this.loading = true;
            await this.fetchCities()
            this.loading = false;
        }, 300),
        async fetchCities(load = false) {
            try {
                const response = await axios.get('/cities', {
                    params: {
                        term: this.searchTerm,
                        page: this.page,
                    },
                });
                if (response.data.data[0]?.TotalCount) {
                    load ? response.data.data[0]?.Addresses.forEach((item) => this.cities.push(item)) : this.cities = response.data.data[0]?.Addresses
                }else{
                    this.cities = [];
                }

            } catch (error) {
                console.error('Error fetching cities from Nova Poshta', error);
            }
        },

        async loadMore() {
            this.page += 1;
            this.loading = true;
            await this.fetchCities();
            this.loading = false;
        },
        select(city){
            this.city = city
            this.searchTerm = ''
            this.cityActive = false;
            this.cities = []
        },
        onScroll: debounce(async function (event) {
              let height = event.target.clientHeight
            if (event.target.scrollTop + height >= event.target.scrollHeight - 10) {
                // Trigger loadMore when scrolled to the bottom (adjust 50 as needed)
                event.target.scrollTop = height - 10;
                this.loadMore();

            }
        }),
    },
};
</script>
