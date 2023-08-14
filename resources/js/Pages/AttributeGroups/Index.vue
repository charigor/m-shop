<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import {Link, router, usePage} from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import BaseButton from '@/Components/Partials/BaseButton.vue'
import { wTrans } from 'laravel-vue-i18n';
import BaseLink from '@/Components/Partials/BaseLink.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTableDrag from "@/Components/Partials/DataTableDrag.vue";
import {defineComponent, defineProps, ref} from "vue";
import {
    mdiAnimationOutline,
    mdiSvg,
    mdiBrightnessAuto

} from "@mdi/js";
const props = defineProps({
    attribute_groups: {
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
    group_type_options: {
        type: Object,
        required: true,
    },
    is_color_group_options: {
        type: Object,
        required: true,
    },

});
const urlPrefix = usePage().props.ziggy.location.split('?')[0];
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.attribute_group.table_fields.id')},
    { value: true , label: 'name',type: 'text',sorting: true,trans : wTrans('page.attribute_group.table_fields.name')},
    { value: true , label: 'amount',type: 'number',sorting: true,trans : wTrans('page.attribute_group.table_fields.amount')},
    { value: true , label: 'position',type: 'number',sorting: true,trans : wTrans('page.attribute_group.table_fields.position')},
    { value: true , label: 'is_color_group',type: 'select',sorting: true,trans : wTrans('page.attribute_group.table_fields.is_color_group')},
    { value: true , label: 'group_type',type: 'select',sorting: true,trans : wTrans('page.attribute_group.table_fields.group_type')},
])
function firstLower(lower){
    return lower && lower[0].toLowerCase() + lower.slice(1) || lower;
}
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
                :icon="mdiBrightnessAuto"
                :title="$t('page.attribute_group.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTableDrag  :data="props.attribute_groups" :filter="props.table_filter" with_show="true" :columns="columns" base-url="/admin/attribute_group" :url-prefix="urlPrefix" table-name="Attribute Groups" delete-title="id">
                <template #create>
                    <BaseLink
                        color="gray"
                        class="ml-auto"
                        small
                        :label="$t('global.create')"
                        :href="'/admin/attribute_group/create'"
                    >
                    </BaseLink>
                </template>
                <template v-slot:select_is_color_group="{ filter,value,key }">
                    <select @change="filter"  aria-label="is_color_group" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.is_color_group_options" :key="option.key">
                            <option :value="option.key">{{$t(`global.${firstLower(option.value)}`)}}</option>
                        </template>
                    </select>
                </template>
                <template v-slot:select_group_type="{ filter,value,key }">
                    <select @change="filter"  aria-label="group_type" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.group_type_options" :key="option.key">
                            <option :value="option.key">{{option.value}}</option>
                        </template>
                    </select>
                </template>
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










