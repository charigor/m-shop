<template>
    <div>
        <div class="w-[500px]">
            <label>Відділення</label>
            <select class="js-warehouse w-full"  ref="selectWarehouseElement"></select>
        </div>
    </div>
</template>

<script>
import 'jquery';
import 'select2/dist/js/select2.full.min.js';
import 'select2/dist/css/select2.css';

export default {
    props: {
        placeholder: {
            type: String,
            default: 'Виберіть відділення',
        },
        idField: {
            type: String,
            default: 'Ref',
        },
        text: {
            type: String,
            default: 'Description'
        },
        totalName: {
            type: String,
            default: 'TotalCount'
        },
        city: Object|null,
        type: Object|null,
        value: Object
    },
    data() {
        return {
            selectedOption: null,
            alreadyLoaded: false,
            routePath: '/novaposhta/warehouses',

            pagination: {
                more: false,
                page: 1,
                limit: 10,
            }
        };
    },
    methods: {
        initSelect2() {
            const _this = this;
            $(this.$refs.selectWarehouseElement).select2({
                ajax: {
                    url: this.routePath,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            SettlementRef: _this.city?.id,
                            TypeOfWarehouseRef: _this.type?.id,
                            page: params.page || 1,
                            limit:  _this.pagination.limit
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        _this.pagination.more = (params.page * 10) < data.info.totalCount;
                        _this.pagination.page = params.page;
                        return {
                            results: data.data.map((item) => ({
                                id: item[_this.idField],
                                text: item[_this.text],
                            })),
                            pagination: {
                                more: _this.pagination.more,
                            },
                        };
                    },
                    cache: false,
                },
                placeholder: this.placeholder,
                minimumInputLength: 0,
                language: {
                    searching: function () {
                        return 'Зачекайте, будь ласка, йде пошук...';
                    },
                    noResults: function () {
                        return 'Результати не знайдено';
                    },
                    inputTooShort: function (args) {
                        var remainingChars = args.minimum - args.input.length;
                        return 'Введіть ще ' + remainingChars + ' символів';
                    },
                },
            });
            $(this.$refs.selectWarehouseElement).on('change', function () {
                const selectedOption = $(this).select2('data')[0];
                const option = { id: selectedOption.id, text: selectedOption.text };
                _this.$emit('selected', option);
            });
        },
    },
    mounted() {
        this.initSelect2();
        if(this.value){
            $(this.$refs.selectWarehouseElement).append($('<option>', {
                value: this.value.id,
                text: this.value.text
            }));
        }
    },
};
</script>
