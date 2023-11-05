<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import {Link, router, usePage} from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue';
import DataTable from "@/Components/Partials/DataTable.vue";
import { wTrans } from 'laravel-vue-i18n';
import BaseLink from '@/Components/Partials/BaseLink.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, ref} from "vue";
import {
    mdiAnimationOutline,
    mdiSvg

} from "@mdi/js";
const props = defineProps({
    feature_values: {
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
});
const urlPrefix = usePage().props.ziggy.location.split('?')[0];

const getLastParam = (url) => {
    let res = url.split("/");
    let pos = res.indexOf('feature');

    return res[pos+1];
}
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.feature_value.table_fields.id')},
    { value: true , label: 'value',type: 'text',sorting: true,trans : wTrans('page.feature_value.table_fields.value')},
])
function firstLower(lower){
    return lower && lower[0].toLowerCase() + lower.slice(1) || lower;
}
defineComponent({
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
                :icon="mdiSvg"
                :title="$t('page.feature_value.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.feature_values" :filter="props.table_filter"  :columns="columns" :base-url="`/admin/feature/${getLastParam(urlPrefix)}/feature_value`" :url-prefix="urlPrefix" table-name="Feature values" delete-title="value">
                <template #create>
                    <BaseLink
                        color="gray"
                        class="ml-auto"
                        small
                        :label="$t('global.create')"
                        :href="`${urlPrefix}/feature_value/create`"
                    >
                    </BaseLink>
                </template>
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










