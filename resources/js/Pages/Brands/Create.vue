<script setup>
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, ref, reactive} from "vue";
import { router,usePage } from '@inertiajs/vue3'
import Dropzone from '@/Components/Partials/Dropzone/Dropzone.vue'
import VueDropzone from '@/Components/Partials/VueDropzone/dropzone.vue'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import CKEditor from '@/Components/Partials/CKEditor/CKEditor.vue';
import TreeMenu from '@/Components/Partials/TreeMenu.vue'
import {
    mdiAnimationOutline,
    mdiSvg

} from "@mdi/js";
import debounce from "lodash.debounce";

import {getActiveLanguage,wTrans} from "laravel-vue-i18n";
import {useMainStore} from "@/stores/main";

const props = defineProps({
    brand: {
        type: Object,
        required: true,
    }
});
let locale = ref(useMainStore().lang)

const form = reactive({
    name: '',
    active: 0,
    lang: {},
    image: [],
});
for(let item in usePage().props.languages){
    form['lang'][usePage().props.languages[item]['code']] = {
        short_description:'',
        description:'',
        meta_title: '',
        meta_description: '',
        meta_keywords: '',
    }
}
const urlPrefix = '/admin/brand';
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
}
defineComponent({
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    SectionMain,
    CKEditor,
    Dropzone,
    TreeMenu
})

const tab = function (code){
    locale.value = code;
}

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiSvg"
                :title="$t('page.brand.title_create')"
                main
            >
            </SectionTitleLineWithButton>

            <ul class="flex flex-wrap  justify-end text-sm font-medium text-center mb-1"  role="tablist">
                <li role="lang" v-for="item in [...$page.props.languages]">
                    <button type="button" @click="tab(item.code)" :class="{ 'bg-gray-300 dark:bg-gray-600': locale === item.code}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                        {{item.code}}
                    </button>
                </li>
            </ul>
            <form @submit.prevent="submit()" class="mb-4 p-8 border border-gray-200 dark:border-gray-700">
                <div class="relative z-0 w-full mb-6 group required">
                    <input v-model="form['name']"  type="text" id="brand_name" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="brand_name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.brand.fields.name')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.name">{{$page.props.errors.name}}</p>
                </div>
                <Switcher :value="form.active" :topLabel="$t('page.brand.fields.active')" name="cat_active" @onChange="(e) => form.active = e" :valueA="0" :valueB="1" :labelA="$t('global.no')"  :labelB="$t('global.yes')"/>

                <div class="relative w-full mb-7 z-10 group">
                 <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t('page.brand.fields.short_description')}}</label>
                    <template v-for="language in $page.props.languages" :key="language.id">
                        <div class="relative z-0 w-full mb-6 pt-7 group" v-show="locale === language.code">
                            <CKEditor v-model="form['lang'][language.code]['short_description']" :lang="useMainStore().lang" :csrf="$page.props.csrf_token"/>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.short_description`]">{{$page.props.errors[`lang.${language.code}.short_description`]}}</p>
                        </div>
                    </template>
                </div>
                <div class="relative w-full mb-7 z-10 group">
                    <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t('page.brand.fields.description')}}</label>
                    <template v-for="language in $page.props.languages" :key="language.id">
                        <div class="relative z-0 w-full mb-6 pt-7 group" v-show="locale === language.code">
                            <CKEditor v-model="form['lang'][language.code]['description']" :lang="useMainStore().lang" :csrf="$page.props.csrf_token"/>
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.description`]">{{$page.props.errors[`lang.${language.code}.description`]}}</p>
                        </div>
                    </template>
                </div>
                <div class="relative w-full mb-7 z-10 group">
                    <VueDropzone :w="Number(250)" :h="Number(250)" viewType="fakeInput" :maxFiles="Number(1)" @removeImage="(file) => form.image = form.image.filter((item) => item !== file)" @loadImages="(file) => form.image.push(file)" path="/admin/brand/storeMedia" :files="[]"></VueDropzone>
                </div>
                <div v-for="language in $page.props.languages" :key="language.id">
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_title']"  type="text" :name="`${language.code}meta_title`" :id="`${language.code}meta_title`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_title`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.brand.fields.meta_title')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_title`]">{{$page.props.errors[`lang.${language.code}.meta_title`]}}</p>
                    </div>
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_description']"  type="text" :name="`${language.code}meta_description`" :id="`${language.code}meta_description`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_description`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.brand.fields.meta_description')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_description`]">{{$page.props.errors[`lang.${language.code}.meta_description`]}}</p>
                    </div>
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_keywords']"  type="text" :name="`${language.code}meta_keywords`" :id="`${language.code}meta_keywords`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_keywords`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.brand.fields.meta_keywords')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_keywords`]">{{$page.props.errors[`lang.${language.code}.meta_keywords`]}}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>
