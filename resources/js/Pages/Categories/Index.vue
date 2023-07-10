<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { Link } from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import BaseButton from '@/Components/Partials/BaseButton.vue'
import { wTrans } from 'laravel-vue-i18n';
import BaseLink from '@/Components/Partials/BaseLink.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTableDrag from "@/Components/Partials/DataTableDrag.vue";
import {defineComponent, defineProps, ref} from "vue";
import {
    mdiAnimationOutline

} from "@mdi/js";
const props = defineProps({
    categories: {
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
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.category.table_fields.id')},
    { value: true , label: 'title',type: 'text',sorting: true,trans : wTrans('page.category.table_fields.title')},
    { value: true , label: 'active',type: 'select',sorting: true,trans : wTrans('page.category.table_fields.active')},
    { value: true ,label: 'created_at',type: 'date',sorting: true,trans : wTrans('page.category.table_fields.created_at')}
])

defineComponent({
    DataTableDrag,
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
                :title="$t('page.category.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTableDrag  :data="props.categories" :filter="props.table_filter" :search="props.table_search" :columns="columns" base-url="/admin/categories" :url-prefix="urlPrefix" table-name="Categories" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        :label="$t('global.create')"
                        :href="'/admin/categories/create'"
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
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










