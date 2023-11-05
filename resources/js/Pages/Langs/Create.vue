<script setup>

import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps,reactive, ref} from "vue";
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiWeb } from "@mdi/js";
import { router } from '@inertiajs/vue3'



const props = defineProps({
    lang: {
        type: Object,
        required: true,
    },
    date_format_options: {
        type: Object,
        required: true,
    },
    date_format_full_options: {
        type: Object,
        required: true,
    },
    active_options: {
        type: Object,
        required: true,
    },
});
const form = reactive({
    name: props.lang.name,
    code: props.lang.code,
    active: props.lang.active,
    date_format: props.lang.date_format,
    date_format_full: props.lang.date_format_full
});
const urlPrefix = '/admin/lang';
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
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
                :icon="mdiWeb"
                :title="$t('page.lang.title_create')"
                main
            >
            </SectionTitleLineWithButton>
            <form @submit.prevent="submit()">
                <div class="required relative z-0 w-full mb-6 group">
                    <input v-model="form.name" type="text" name="name" id="name" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.lang.fields.name')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.name">{{$page.props.errors.name}}</p>
                </div>
                <div class="required relative z-0 w-full mb-6 group">
                    <input v-model="form.code" type="text" name="code" id="code" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="code" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.lang.fields.code')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.code">{{$page.props.errors.code}}</p>
                </div>

                <div class="required relative z-0 w-full mb-6 group">
                    <div style="height: 2px" class="relative z-0 w-full mb-3 group">
                        <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.lang.fields.date_format')}}</label>
                    </div>
                    <select v-model="form.date_format"  aria-label="date_format" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.date_format_options" :key="option.key">
                            <option>{{option.value}}</option>
                        </template>
                    </select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.date_format">{{$page.props.errors.date_format}}</p>
                </div>

                <div class="required relative z-0 w-full mb-6 group">
                    <div style="height: 2px" class="relative z-0 w-full mb-3 group">
                        <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.lang.fields.date_format_full')}}</label>
                    </div>
                    <select v-model="form.date_format_full" aria-label="date_format_full" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.date_format_full_options" :key="option.key">
                            <option>{{option.value}}</option>
                        </template>
                    </select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.date_format_full">{{$page.props.errors.date_format_full}}</p>
                </div>
                <div class="required relative z-0 w-full mb-6 group">
                    <div style="height: 2px" class="relative z-0 w-full mb-3 group">
                        <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.lang.fields.active')}}</label>
                    </div>
                    <select v-model="form.active"  aria-label="active" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template  v-for="option in props.active_options" :key="option.key">
                            <option :value="option.key">{{option.value}}</option>
                        </template>
                    </select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.active">{{$page.props.errors.active}}</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>

        </SectionMain>
    </LayoutAuthenticated>
</template>
