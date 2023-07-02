<script setup>

import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { Link } from '@inertiajs/vue3';
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import BaseButton from '@/Components/Partials/BaseButton.vue'
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
    { value: true , label: 'id', type: 'number',sorting: true},
    { value: true , label: 'name',type: 'text',sorting: true},
    { value: true ,label: 'created_at',type: 'date',sorting: true}
])

defineComponent({
    DataTableDrag,
    SectionTitleLineWithButton,
    SectionMain,
    BaseLink

})

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                title="Categories"
                main
            >
            </SectionTitleLineWithButton>
            <DataTableDrag  :data="props.categories" :filter="props.filter" :search="props.search" :columns="columns" base-url="/admin/categories" :url-prefix="urlPrefix" table-name="Categories" delete-title="name">
                <template #create>
                    <BaseLink
                        color="gray"
                        small
                        label="Create"
                        :href="'/admin/categories/create'"
                    >
                    </BaseLink>
                </template>
            </DataTableDrag>
        </SectionMain>
    </LayoutAuthenticated>
</template>










