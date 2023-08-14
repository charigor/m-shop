<script setup>
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, ref, reactive} from "vue";
import { router,usePage } from '@inertiajs/vue3'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import debounce from "lodash.debounce";
import BaseLink from '@/Components/Partials/BaseLink.vue'
import {getActiveLanguage,wTrans} from "laravel-vue-i18n";
import {useMainStore} from "@/stores/main";
import {
    mdiArrowLeftBoldOutline,
    mdiFormatColumns

} from "@mdi/js";


const props = defineProps({
    feature: {
        type: Object,
        required: true,
    }
});
let locale = ref(useMainStore().lang)

const form = reactive({
    lang: {},
});
for(let item in usePage().props.languages){
    form['lang'][usePage().props.languages[item]['code']] = {
        name: ''
    }
}
const urlPrefix = '/admin/feature';
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
}
defineComponent({
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    SectionMain,
})

const tab = function (code){
    locale.value = code;
}

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFormatColumns"
                :title="$t('page.feature.title_create')"
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

                <div v-for="language in $page.props.languages" :key="language.id">
                    <div class="relative z-0 w-full mb-6 group required" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['name']"  type="text" :name="`${language.code}name`" :id="`${language.code}name`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}name`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.feature.fields.name')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.name`]">{{$page.props.errors[`lang.${language.code}.name`]}}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>
            <BaseLink
                color="gray"
                class="ml-auto"
                small
                :icon="mdiFormatColumns"
                :label="$t('global.back_to_list')"
                href="/admin/feature"
            >
            </BaseLink>
        </SectionMain>
    </LayoutAuthenticated>
</template>
