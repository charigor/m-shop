<script setup>

import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps,reactive, ref} from "vue";
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiAccountBoxMultipleOutline } from "@mdi/js";
import { router } from '@inertiajs/vue3'



const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
});
const form = reactive({
    name: props.role.name,
    permissions: props.role.permissions
});
const urlPrefix = '/admin/roles';
const submit = () => {
    router.put(`${urlPrefix}/${props.role.id}/update`, form, {preserveState: true})
}
defineComponent({
    SectionTitleLineWithButton,
    SectionMain
})
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.role.title_edit')"
                main
            >
            </SectionTitleLineWithButton>
            <form @submit.prevent="submit()">
                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.name" type="text" name="name" id="name" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.role.fields.name')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.name">{{$page.props.errors.name}}</p>
                </div>
                <div class="z-0 w-full mb-6">
                    <div style="height: 2px" class="relative z-0 w-full group">
                        <label for="permissions" class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.role.fields.permissions')}}</label>
                    </div>
                    <v-select multiple id="permissions" :options="props.permissions" :reduce="permission => permission.id" v-model="form.permissions" label="name" class="mt-3 py-2"></v-select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.permissions">{{$page.props.errors.permissions}}</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.edit')}}</button>
                </div>
            </form>

        </SectionMain>
    </LayoutAuthenticated>
</template>











