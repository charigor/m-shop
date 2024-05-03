<template>
    <div>
        <div class="w-[500px]">
            <label>Тип Відділення</label>
            <select class="js-warehouse-type w-full" v-model="selectedOption" ref="selectWarehouseTypeElement">
                <option :value="null" disabled hidden>Выберите тип відділення</option>
                <option v-for="option in options" :key="option.id" :value="option.id">{{ option.text }}</option>
            </select>
        </div>
    </div>
</template>

<script>
import 'jquery';
import 'select2/dist/js/select2.full.min.js';
import 'select2/dist/css/select2.css';

export default {
    data() {
        return {
            options: [],
            selectedOption: null,
            routePath: '/novaposhta/warehouseTypes'
        };
    },
    props: {
        placeholder: {
            type: String,
            default: 'Оберіть тип відділення',
        },
        idField : {
            type: String,
            default : 'Ref'
        },
        text:{
            type: String,
            default : 'Description'
        },
        value: Object
    },
    methods: {
        loadOptions() {
            axios.post(this.routePath, {})
                .then(response => {
                    this.options = response.data.map(option => ({ id: option[this.idField], text: option[this.text] }));
                    this.initSelect2();
                })
                .catch(error => {
                    console.error('Error loading options:', error);
                });
        },
        initSelect2() {
            let _this = this;
            $(this.$refs.selectWarehouseTypeElement).select2().on('change', function(event)  {
                let object = $(this).select2('data')[0]
                _this.selectedOption = {'id' : object.id, 'text' : object.text};
                _this.$emit('selected', _this.selectedOption);
            });
        },
    },
    mounted() {
        this.loadOptions();
        if(this.value) {
            $(this.$refs.selectWarehouseTypeElement).val(this.value.id).trigger('change');
            $(this.$refs.selectWarehouseTypeElement).append($('<option>', {
                value: this.value.id,
                text: this.value.text
            }));
        }
    },
    watch: {
        value(newValue) {
            if (newValue) {
                $(this.$refs.selectWarehouseTypeElement).select2('data', { id: newValue.id, text: newValue.text })
            }
        }
    },
};
</script>
