<script setup>

import { useMainStore } from "@/stores/main.js";
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import CKEditor from '@/Components/Partials/CKEditor/CKEditor.vue';
import SectionMain from '@/Components/Partials/SectionMain.vue'
import TreeMenu from '@/Components/Partials/TreeMenu.vue'
import {defineComponent, defineProps,ref,computed, reactive} from "vue";
import {router, usePage} from '@inertiajs/vue3'
import VueDropzone from '@/Components/Partials/VueDropzone/dropzone.vue'
import Switcher from "@/Components/Partials/Switcher.vue";
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import debounce from "lodash.debounce";
import { getActiveLanguage,wTrans } from 'laravel-vue-i18n';
import {
    mdiAnimationOutline

} from "@mdi/js";
const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
});
let locale = ref(useMainStore().lang)

const form = reactive({
    parent_id: +props.category.parent_id,
    active: props.category.active,
    cover_image: props.category.cover_image.map(i => i.file_name),
    menu_thumbnail: props.category.menu_thumbnail.map(i => i.file_name),
    lang: {},
    _method: 'put'
});

for(let item in usePage().props.languages){
    let code = usePage().props.languages[item]['code'];
    if(props.category.translation.hasOwnProperty(code)){
        form['lang'][code] = props.category.translation[code]
    }else{
        form['lang'][code] =  {
            title : '',
            description:'',
            link_rewrite:'',
            meta_title: '',
            meta_description: '',
            meta_keywords: '',
        }
    }
}
const urlPrefix = '/admin/categories';
const submit = () => {
    router.put(`${urlPrefix}/${props.category.id}`, form, {preserveState: true})
}
defineComponent({
    TreeMenu,
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    SectionMain,
    CKEditor,
    Switcher

})
const tab = function (code){
    locale.value = code;
}

const changeSlug = debounce(async (lang) => {

    let response =  await  axios.post('/admin/categories/slug', {'title': form['lang'][lang]['title'] })
    form['lang'][lang]['link_rewrite'] = response.data.slug
},200);
const changeParent = (event) => {
    form.parent_id = event?.target ? event.target.value: event;
}
const accordionTrigger = ref(true)

const categories = ref([{id: 0,parent_id: null,translation: [{'id': null, title: wTrans('page.category.root')}],children: props.categories}]);

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAnimationOutline"
                :title="$t('page.category.title_edit')"
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

                <Switcher :value="form.active" :topLabel="$t('page.category.fields.active')" name="cat_active" @onChange="(e) => form.active = e" :valueA="0" :valueB="1" :labelA="$t('global.no')"  :labelB="$t('global.yes')"/>
                <div v-for="language in $page.props.languages" :key="language.id">
                    <div class="relative z-0 w-full mb-6 group required" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['title']" @input="changeSlug(language.code)" type="text" :name="`${language.code}title`" :id="`${language.code}title`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}title`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.title')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.title`]">{{$page.props.errors[`lang.${language.code}.title`]}}</p>
                    </div>

                    <div class="relative z-0 w-full mb-6 group required" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['link_rewrite']" type="text" :name="`${language.code}link_rewrite`" :id="`${language.code}link_rewrite`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}link_rewrite`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.link_rewrite')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.link_rewrite`]">{{$page.props.errors[`lang.${language.code}.link_rewrite`]}}</p>
                    </div>
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_title']"  type="text" :name="`${language.code}meta_title`" :id="`${language.code}meta_title`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_title`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.meta_title')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_title`]">{{$page.props.errors[`lang.${language.code}.meta_title`]}}</p>
                    </div>
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_description']"  type="text" :name="`${language.code}meta_description`" :id="`${language.code}meta_description`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_description`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.meta_description')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_description`]">{{$page.props.errors[`lang.${language.code}.meta_description`]}}</p>
                    </div>
                    <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['meta_keywords']"  type="text" :name="`${language.code}meta_keywords`" :id="`${language.code}meta_keywords`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}meta_keywords`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.meta_keywords')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_keywords`]">{{$page.props.errors[`lang.${language.code}.meta_keywords`]}}</p>
                    </div>
                </div>
                <div class="mb-5">
                    <h2>
                        <button type="button" @click.prevent="accordionTrigger = !accordionTrigger" class="flex items-center justify-between w-full py-4 px-4 font-medium text-left text-gray-500 border border-b-1 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                            {{$t('page.category.fields.parent')}}
                            <span :class="{ 'rotate-180': accordionTrigger }">
                                <svg  data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                                </svg>
                            </span>
                        </button>
                    </h2>
                    <div v-show="accordionTrigger" class="flex items-center justify-between w-full py-4 px-4 font-medium text-left text-gray-500 border border-b-1 border-gray-200 rounded-b-xl   dark:border-gray-700 dark:text-gray-400">
                        <template v-for="node in categories">
                            <TreeMenu  @input="changeParent" :parent="form.parent_id" :nodes="node.children" :value="node.id" :label="node.translation[0].title" ></TreeMenu>
                        </template>
                    </div>
                </div>
                <div class="relative w-full mb-7 z-10 group">
                <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t('page.category.fields.description')}}</label>
                    <template  v-for="language in $page.props.languages" :key="language.id">

                        <div class="relative z-0 w-full mb-6 pt-7 group" v-show="locale === language.code">
                            <CKEditor v-model="form['lang'][language.code]['description']" :lang="language.code"  :csrf="$page.props.csrf_token"/>
                        </div>
                    </template>
                </div>

                <div class="relative w-full mb-7 z-10 group">
                    <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t('page.category.fields.cover_image')}}</label>
                    <div class="relative row mb-6 group pt-7">
                        <VueDropzone :w="Number(250)" :h="Number(250)" :maxFiles="1" viewType="fakeInput" @removeImage="(file) => form.cover_image = form.cover_image.filter((item) => item !== file)" @loadImages="(file) => form.cover_image.push(file)" path="/admin/categories/storeMedia" :files="props.category.cover_image"></VueDropzone>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.cover_image">{{$page.props.errors.cover_image}}</p>
                    </div>
                </div>

                <div class="relative w-full mb-7 z-10 group">
                    <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t('page.category.fields.menu_thumbnail')}}</label>
                    <div class="relative row mb-6 group pt-7">
                        <VueDropzone :w="Number(150)" :h="Number(150)" :multiple="Boolean(1)" :maxFiles="1" viewType="square" @removeImage="(file) => form.menu_thumbnail = form.menu_thumbnail.filter((item) => item !== file)" @loadImages="(file) => form.menu_thumbnail.push(file)" path="/admin/categories/storeMedia" :files="props.category.menu_thumbnail" :csrf="$page.props.csrf_token"></VueDropzone>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.menu_thumbnail">{{$page.props.errors.menu_thumbnail}}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.edit')}}</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>











