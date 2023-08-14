<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import {Link, router, usePage} from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import { wTrans } from 'laravel-vue-i18n';
import BaseLink from '@/Components/Partials/BaseLink.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTableDrag from "@/Components/Partials/DataTableDrag.vue";
import {defineComponent, defineProps, ref} from "vue";
import {
    mdiFormatColumns

} from "@mdi/js";
const pageName = 'feature'
const props = defineProps({
    features: {
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
    }

});
const urlPrefix = usePage().props.ziggy.location.split('?')[0];
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.feature.table_fields.id')},
    { value: true , label: 'name',type: 'text',sorting: true,trans : wTrans('page.feature.table_fields.name')},
    { value: true , label: 'amount',type: 'number',sorting: true,trans : wTrans('page.feature.table_fields.amount')},
    { value: true , label: 'position',type: 'number',sorting: true,trans : wTrans('page.feature.table_fields.position')},
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
                :icon="mdiFormatColumns"
                :title="$t(`page.${pageName}.title_plural`)"
                main
            >
            </SectionTitleLineWithButton>

            <DataTableDrag  :data="props.features" :filter="props.table_filter" with_show="true" :columns="columns" :base-url="`/admin/${pageName}`" :url-prefix="urlPrefix" :table-name="$t(`page.${pageName}.title_plural`)" delete-title="name">

                <template #create>
                    <BaseLink
                        color="gray"
                        class="ml-auto"
                        small
                        :label="$t('global.create')"
                        :href="`/admin/${pageName}/create`"
                    >
                    </BaseLink>
                </template>
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










