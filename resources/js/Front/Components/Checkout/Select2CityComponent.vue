<template>
    <div>
        <div class="w-[500px]">
            <label>Місто</label>
            <select class="js-example-ajax w-full"  v-model="selectedOption" ref="selectCityElement"></select>
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
            selectedOption: null,
            routePath: '/novaposhta/cities',
            pagination: {
                more: false,
                page: 1,
                limit: 10,
            }
        };
    },
    props: {
        placeholder: {
            type: String,
            default: 'Оберіть населений пункт України',
        },
        optionName:{
            type: String,
            default : 'Addresses'
        },
        idField : {
            type: String,
            default : 'Ref'
        },
        text:{
            type: String,
            default : 'Present'
        },
        value: Object
    },
    methods: {
        initSelect2() {
            let _this = this
            $('.js-example-ajax').select2({
                ajax: {
                    url: this.routePath,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            limit:  _this.pagination.limit
                        };
                    },
                    processResults: function (data,params) {
                        if (Array.isArray(data.data)) {
                            const addresses = data.data[0][_this.optionName];
                            const total = data.data[0]['TotalCount'];
                            params.page = params.page || 1;
                            _this.pagination.more = (params.page * 10) < total;
                            _this.pagination.page = params.page;
                            return {
                                results: addresses.map((item) => ({
                                    id: item[_this.idField],
                                    text: item[_this.text],
                                })),
                                pagination: {
                                    more: _this.pagination.more,
                                },
                            };
                        }else{
                            return { results: [] };
                        }

                    },
                    cache: true,
                },
                placeholder: this.placeholder,
                minimumInputLength: 3,
                language: {
                    searching: function () {
                        return 'Зачекайте, будь ласка, йде пошук...';
                    },
                    noResults: function () {
                        return 'Результати не знайдено';
                    },
                    inputTooShort: function () {
                        return "Будь ласка, введіть від 3 символів для пошуку";
                    }
                }
            }).on('change', function() {
                _this.selectedOption = {'id' : $(this).val(), 'text' : $(this).text()};
                _this.$emit('selected', _this.selectedOption);
            });
        },
    },
    mounted() {
        this.initSelect2();
        if(this.value) {
            $(this.$refs.selectCityElement).append($('<option>', {
                value: this.value.id,
                text: this.value.text
            }));
        }
    },
    // watch: {
    //     value(newValue) {
    //         if (newValue) {
    //             $(this.$refs.selectCityElement).val(newValue.id).text(newValue.text).trigger('change');
    //         }
    //     }
    // },
};
</script>
