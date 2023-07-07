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
const props = defineProps({
    categories: {
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
const urlPrefix = window.location.href.split('?')[0];
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans : wTrans('page.category.table_fields.id')},
    { value: true , label: 'title',type: 'text',sorting: true,trans : wTrans('page.category.table_fields.title')},
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
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.category.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTableDrag  :data="props.categories" :filter="props.filter" :search="props.search" :columns="columns" base-url="/admin/categories" :url-prefix="urlPrefix" table-name="Categories" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        :label="$t('global.create')"
                        :href="'/admin/categories/create'"
                    >
                    </BaseLink>
                </template>
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










