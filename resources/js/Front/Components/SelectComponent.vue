<template>
    <div>
        <div class="w-[500px]">

            <div>

            </div>

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
.multiselect__option--disabled {
    pointer-events: none;
}
</style>
<script>
// import multiselect from 'vue-multiselect'
export default {
    components: {
        // multiselect,
    },
    data() {
        return {
            options: [
                { id: 1, name: 'Option 1' },
                { id: 2, name: 'Option 2' },
                { id: 3, name: 'Option 3' },],
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

        },
        totalName: {
            type: String,
        }

    },
    computed: {
        // hasNextPage() {
        //     return (this.options?.length || 0) < this.total
        // },
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
                ul.scrollTop = scrollTop

            }
        },
        loadMoreOptions() {
            if (!this.loading) {
                this.page++;
                this.loadOptions(this.term);
            }
        },
        loadOptions(query) {
            this.loading = true;
            axios.post(this.url, {
                q: query,
                page: this.page,
                limit: this.limit
            })
                .then(response => {
                    const [data] = [...response.data.data];

                    if (this.page === 1) {
                        this.options = data?.[this.optionName];
                    } else {
                        this.options = [...this.options, ...data?.[this.optionName]];
                    }
                    this.total = data?.[this.totalName] || 0

                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                });
        },
    },
    watch: {
        // selectedOption: function () {
        //     // Emit this information to the parents component
        //     this.$emit("selected", this.selectedOption);
        // }
    },
    mounted() {
        // this.observer = new IntersectionObserver(this.infiniteScroll)
    },
    beforeDestroy() {

        // this.observer.disconnect();

    },
};
</script>
