<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { Link } from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import { wTrans } from 'laravel-vue-i18n';
import BaseLink from '@/Components/Partials/BaseLink.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTable from "@/Components/Partials/DataTable.vue";
import {defineComponent, defineProps, ref} from "vue";
import {
    mdiAnimationOutline

} from "@mdi/js";
const pageName = 'product';
const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    table_filter: {
        type: [Object,null],
        required: true,
    },
    table_search: {
        type: [String,null],
        required: true,
    },
    active_options: {
        type: Object,
        required: true,
    },
});
const urlPrefix = window.location.href.split('?')[0];
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans(`page.${pageName}.table_fields.id`)},
    { value: true ,label: 'image',type: 'media',sorting: false,trans: wTrans(`page.${pageName}.table_fields.image`)},
    { value: true , label: 'name',type: 'text',sorting: true,trans : wTrans(`page.${pageName}.table_fields.name`)},
    { value: true , label: 'reference',type: 'text',sorting: true,trans : wTrans(`page.${pageName}.table_fields.reference`)},

    // { value: true , label: 'price',type: 'number',sorting: true,trans : wTrans(`page.${pageName}.table_fields.price`)},
    { value: true , label: 'quantity',type: 'number',sorting: true,trans : wTrans(`page.${pageName}.table_fields.quantity`)},
    { value: true , label: 'active',type: 'select',sorting: true,trans : wTrans(`page.${pageName}.table_fields.active`)},
    { value: true ,label: 'created_at',type: 'date',sorting: true,trans : wTrans(`page.${pageName}.table_fields.created_at`)},
    { value: true ,label: 'updated_at',type: 'date',sorting: true,trans : wTrans(`page.${pageName}.table_fields.updated_at`)}
])
defineComponent({
    DataTable,
    SectionTitleLineWithButton,
    SectionMain,
    BaseLink,
    wTrans

})

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAnimationOutline"
                :title="$t(`page.${pageName}.title_plural`)"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.products" :filter="props.table_filter" :search="props.table_search" :columns="columns" :base-url="`/admin/${pageName}`" :url-prefix="urlPrefix" :table-name="$t(`page.${pageName}.title_plural`)" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        :label="$t('global.create')"
                        :href="`/admin/${pageName}/create`"
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
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










