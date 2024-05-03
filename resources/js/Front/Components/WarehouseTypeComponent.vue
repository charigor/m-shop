<template>
    <div>
        <div class="w-[500px]">
            <label>Тип Відділення</label>
            <v-select
                v-model="selectedOption"
                :options="options"
                :label="label"
                :clearable="false"
                class="w-full"
            >
            </v-select>

        </div>
    </div>
</template>
<style>
body .vs__dropdown-menu {
    max-height: 300px
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
            selectedOption: null,
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
        city: {
            type: Object
        },
        warehouseType: {
            type: Object
        }

    },
    methods: {

        loadOptions() {
            axios.post(this.url, {})
                .then(response => {
                    this.options = response.data;
                })
                .catch(error => {
                    console.log(error)
                });
        },
    },
    watch: {
        selectedOption: function () {
            // Emit this information to the parents component
            this.$emit("selected", this.selectedOption);
        },
        city: function () {
            this.selectedOption = null
            this.loadOptions()
        },
    },
    mounted() {
        let checkoutProgress = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
        this.selectedOption = checkoutProgress?.warehouseType ?? null
        this.loadOptions()
    },
};
</script>
