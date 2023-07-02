<script setup>


import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTable from "@/Components/Partials/DataTable.vue";
import BaseLink from '@/Components/Partials/BaseLink.vue'
import {defineComponent, defineProps, ref} from "vue";
import { Link } from '@inertiajs/vue3';
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiAccountBoxMultipleOutline } from "@mdi/js";
const props = defineProps({
    roles: {
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
    { value: true , label: 'id', type: 'number',sorting: true},
    { value: true , label: 'name',type: 'text',sorting: true},
    { value: true ,label: 'created_at',type: 'date',sorting: true}
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
                :icon="mdiAccountBoxMultipleOutline"
                title="Roles"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.roles" :filter="props.filter" :search="props.search" :columns="columns" base-url="/admin/roles" :url-prefix="urlPrefix" table-name="Roles" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        label="Create"
                        :href="'/admin/roles/create'"
                    >
                    </BaseLink>
                </template>
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










