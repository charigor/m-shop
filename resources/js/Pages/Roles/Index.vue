<script setup>


import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTable from "@/Components/Partials/DataTable.vue";
import BaseLink from '@/Components/Partials/BaseLink.vue'
import {defineComponent, defineProps, ref} from "vue";
import {Link, router, usePage} from '@inertiajs/vue3';
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiAccountBoxMultipleOutline } from "@mdi/js";
import {wTrans} from "laravel-vue-i18n";
const props = defineProps({
    roles: {
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
const columns = ref(
    [
    { value: true , label: 'id', type: 'number',sorting: true,trans: wTrans('page.role.table_fields.id')},
    { value: true , label: 'name',type: 'text',sorting: true,trans: wTrans('page.role.table_fields.name')},
    { value: true , label: 'created_at',type: 'date',sorting: true,trans: wTrans('page.role.table_fields.created_at')}
])

defineComponent({
    DataTable,
    SectionTitleLineWithButton,
    SectionMain,
    BaseLink
})
const urlPrefix = usePage().props.ziggy.location.split('?')[0];

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.role.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.roles" :filter="props.table_filter" :search="props.table_search" :columns="columns" base-url="/admin/roles" :url-prefix="urlPrefix" table-name="Roles" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        :label="$t('global.create')"
                        :href="'/admin/roles/create'"
                    >
                    </BaseLink>
                </template>
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










