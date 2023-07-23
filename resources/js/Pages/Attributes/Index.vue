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
    mdiAnimationOutline,
    mdiSvg

} from "@mdi/js";
const props = defineProps({
    attributes: {
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
const urlPrefix = window.location.href.split('?')[0];

const getLastParam = (url) => {
    let res = url.split("/");
    let pos = res.indexOf('attribute_group');

    return res[pos+1];
}
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.attribute.table_fields.id')},
    { value: true , label: 'name',type: 'text',sorting: true,trans : wTrans('page.attribute.table_fields.name')},
    { value: true , label: 'color',type: 'color',sorting: true,trans : wTrans('page.attribute.table_fields.color')},
    { value: true , label: 'position',type: 'number',sorting: true,trans : wTrans('page.attribute.table_fields.position')},
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
                :icon="mdiSvg"
                :title="$t('page.attribute.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTableDrag  :data="props.attributes" :filter="props.table_filter"  :columns="columns" :base-url="`/admin/attribute_group/${getLastParam(urlPrefix)}/attribute`" :url-prefix="urlPrefix" table-name="Attributes" delete-title="id">
                <template #create>
                    <BaseLink
                        color="gray"
                        class="ml-auto"
                        small
                        :label="$t('global.create')"
                        :href="`${urlPrefix}/attribute/create`"
                    >
                    </BaseLink>
                </template>
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










