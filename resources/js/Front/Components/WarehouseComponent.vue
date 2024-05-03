<template>
    <div>
        <div class="w-[500px]">
            <label>Відділення</label>

            <v-select
                v-model="selectedOption"
                :options="options"
                :label="label"
                @search="onSearch"
                @open="onOpen"
                @close="onClose"
                :clearable="false"
                class="w-full"
            >
                <template #list-footer>
                    <li v-show="hasNextPage" ref="load" class="loader">
                        Loading more options...
                    </li>
                </template>
            </v-select>

        </div>
    </div>
</template>
<style>
body .vs__dropdown-menu {
    max-height: 300px
}

.loader {
    text-align: center;
    color: #bbbbbb;
}

</style>
<script>
import vSelect from 'vue-select';


export default {
    components: {
        vSelect,
    },
    data() {
        return {
            options: [],
            term: '',
            selectedOption: null,
            total: 0,
            page: 1,
            loading: false,
            isActive: false,
            observer: null,
            limit: 10
        };
    },
    props: {
        url: {
            type: String
        },
        label: {
            type: String
        },
        value: {
            type: String
        },
        optionName: {
            type: String,
            required: true
        },
        totalName: {
            type: String,
        },
        city: {
            type: Object
        },
        type: {
            type: Object
        },
    },
    computed: {
        hasNextPage() {
            return (this.options?.length || 0) < this.total
        },
    },
    methods: {
        onSearch(query) {
            if (!query) {
                this.cities = [];
                return;
            }
            this.page = 1;
            this.term = query;
            this.loadOptions(query);
        },
        async onOpen() {
            await this.$nextTick()
            this.observer.observe(this.$refs.load)
        },
        onClose() {
            this.observer.disconnect()
        },
        async infiniteScroll([{isIntersecting, target}]) {
            if (isIntersecting) {
                const ul = target.offsetParent

                const scrollTop = target.offsetParent.scrollTop
                this.loadMoreOptions()
            }
        },
        loadMoreOptions() {
            if (!this.loading) {
                this.page++;
                this.loadOptions(this.term);
            }
        },
        loadOptions(query = '') {
            this.loading = true;
            let object = {
                q: query,
                page: this.page,
                ref: this.city?.Ref,
                type: this.type?.Ref,
                limit: this.limit
            }
            axios.post(this.url, object)
                .then(response => {
                    if (this.page === 1) {
                        this.options = response.data?.data;
                    } else {
                        this.options = [...this.options, ...response.data?.data];
                    }
                    this.total = response.data.info?.totalCount || 0

                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                });
        },
    },
    watch: {
        selectedOption: function () {
            // Emit this information to the parents component
            this.$emit("selected", this.selectedOption);
        },
        city: function () {
            let checkoutProgress = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
            this.selectedOption = checkoutProgress?.warehouse ?? null
            this.loadOptions()
        },
        type: function () {
            let checkoutProgress = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
            this.selectedOption = checkoutProgress?.warehouse ?? null
            this.loadOptions()
        }
    },
    mounted() {
        let checkoutProgress = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
        this.selectedOption = checkoutProgress?.warehouse ?? null
        this.observer = new IntersectionObserver(this.infiniteScroll)
    },
    beforeDestroy() {

        this.observer.disconnect();

    },
};
</script>
