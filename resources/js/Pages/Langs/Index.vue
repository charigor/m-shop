<script setup>


import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTable from "@/Components/Partials/DataTable.vue";
import BaseLink from '@/Components/Partials/BaseLink.vue'
import {defineComponent, defineProps, ref} from "vue";
import { Link } from '@inertiajs/vue3';
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiWeb } from "@mdi/js";
import {wTrans} from "laravel-vue-i18n";
const props = defineProps({
    langs: {
        type: Object,
        required: true,
    },
    date_format_options: {
        type: Array,
        required: true,
    },
    date_format_full_options: {
        type: Array,
        required: true,
    },
    active_options: {
        type: Object,
        required: true,
    },
    filter: {
        type: [Object,null],
        required: true,
    },
    search: {
        type: [String,null],
        required: true,
    },
});
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true, trans: wTrans('page.lang.table_fields.id')},
    { value: true , label: 'name',type: 'text',sorting: true, trans: wTrans('page.lang.table_fields.name')},
    { value: true ,label: 'code',type: 'text',sorting: true, trans: wTrans('page.lang.table_fields.code')},
    { value: true ,label: 'active',type: 'select',sorting: true, trans: wTrans('page.lang.table_fields.active')},
    { value: true ,label: 'date_format',type: 'select',sorting: false ,trans: wTrans('page.lang.table_fields.date_format')},
    { value: true ,label: 'date_format_full',type: 'select',sorting: false,trans: wTrans('page.lang.table_fields.date_format_full')}
])

defineComponent({
    DataTable,
    SectionTitleLineWithButton,
    SectionMain,
    BaseLink
})
const urlPrefix = window.location.href.split('?')[0];

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>

            <SectionTitleLineWithButton
                :icon="mdiWeb"
                :title="$t('page.lang.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.langs" :filter="props.filter" :search="props.search" :columns="columns" base-url="/admin/lang" :url-prefix="urlPrefix" table-name="Langs" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        :label="$t('global.create')"
                        :href="'/admin/lang/create'"
                    >
                    </BaseLink>
                </template>
                <template v-slot:select_active="{ filter,value,key }">
                    <select @change="filter"  aria-label="active" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.active_options" :key="option.key">
                            <option :value="option.key">{{option.value}}</option>
                        </template>
                    </select>
                </template>
                <template v-slot:select_date_format="{ filter,value,key }">
                    <select @change="filter"  aria-label="date_format" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.date_format_options" :key="option.key">
                            <option :value="option.key">{{option.value}}</option>
                        </template>
                    </select>
                </template>
                <template v-slot:select_date_format_full="{ filter,value,key }">
                    <select @change="filter"  aria-label="date_format_full" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.date_format_full_options" :key="option.key">
                            <option :value="option.key">{{option.value}}</option>
                        </template>
                    </select>
                </template>
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










