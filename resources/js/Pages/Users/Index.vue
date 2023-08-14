<script setup>


import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import DataTable from "@/Components/Partials/DataTable.vue";
import BaseButton from "@/Components/Partials/BaseButton.vue";
import BaseLink from "@/Components/Partials/BaseLink.vue";
import {defineComponent, defineProps, ref} from "vue";
import { Link, router, usePage} from '@inertiajs/vue3';
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiAccountBoxMultipleOutline } from "@mdi/js";
import {wTrans} from "laravel-vue-i18n";
const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
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
let columns = ref(
    [
    { value: true ,label: 'id',type: 'number',sorting: true,trans: wTrans('page.user.table_fields.id')},
    { value: true ,label: 'avatar',type: 'media',sorting: false,trans: wTrans('page.user.table_fields.avatar')},
    { value: true ,label: 'name',type: 'text',sorting: true,trans: wTrans('page.user.table_fields.name')},
    { value: true ,label: 'email',type: 'text',sorting: true,trans: wTrans('page.user.table_fields.email')},
    { value: true ,label: 'roles',type: 'select',sorting: false,trans: wTrans('page.user.table_fields.role')},
    { value: true ,label: 'created_at',type: 'date',sorting: true,trans: wTrans('page.user.table_fields.created_at')}
])

defineComponent({
    DataTable,
    SectionTitleLineWithButton,
    SectionMain,
    BaseButton,
    BaseLink
})

const urlPrefix = usePage().props.ziggy.location.split('?')[0];
console.log( urlPrefix)
const baseUrl = '/admin/users'
const deleteTitle = 'name'
const modelName = 'Users';
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.user.title_plural')"
                main
            >
            </SectionTitleLineWithButton>
            <DataTable  :data="props.users" :filter="props.table_filter" with_total="true" :search="props.table_search" :columns="columns" base-url="/admin/users" :url-prefix="urlPrefix" table-name="Users" delete-title="name">
                <template  #create>
                    <BaseLink
                        color="gray"
                        small
                        :href="`/admin/users/create`"
                        :label="$t('global.create')"
                    >
                    </BaseLink>
                </template>
                <template v-slot:select="{ filter,value,key }">
                    <select @change="filter"  aria-label="roles" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                            <template  v-for="option in props.roles" :key="option.id">
                                <option :value="option.id">{{option.name}}</option>
                            </template>
                    </select>
                </template>
            </DataTable>
        </SectionMain>
    </LayoutAuthenticated>
</template>










