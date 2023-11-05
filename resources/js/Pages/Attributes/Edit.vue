<script setup>
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import BaseLink from '@/Components/Partials/BaseLink.vue'
import {defineComponent, defineProps, ref, reactive, computed} from "vue";
import { router,usePage } from '@inertiajs/vue3'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { ColorPicker } from 'vue-color-kit'
import 'vue-color-kit/dist/vue-color-kit.css'
import {
    mdiAnimationOutline,
    mdiSvg,
    mdiArrowLeftBoldOutline


} from "@mdi/js";
import debounce from "lodash.debounce";

import {getActiveLanguage,wTrans} from "laravel-vue-i18n";
import {useMainStore} from "@/stores/main";

const props = defineProps({
    attribute: {
        type: Object,
        required: true,
    },
    group_type_options: {
        type: Object,
        required: true,
    },
    parent_route_id: {
        type: Number,
        required: true,
    }

});
let locale = ref(useMainStore().lang)

const form = reactive({
    color: props.attribute.color,
    attribute_group_id: props.attribute.attribute_group_id,
    lang: {},
    _method: 'put'
});

for(let item in usePage().props.languages){
    let code = usePage().props.languages[item]['code'];
    if(props.attribute.translation.hasOwnProperty(code)){
        form['lang'][code] = props.attribute.translation[code]
    }else{
        form['lang'][code] =  {
            name:'',
        }
    }
}
const urlPrefix = '/admin/attribute_group/'+props.parent_route_id+'/attribute';

const suckerCanvas = ref(null);
const showcolorpicker =  ref(false);
const submit = () => {
    router.put(`${urlPrefix}/${props.attribute.id}`, form, {preserveState: true})
}
defineComponent({
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    SectionMain,
    ColorPicker
})

const tab = function (code){
    locale.value = code;
}
const changeColor = (color) => {
    form.color = color.hex;
}
const showHideColor = computed(() => {
    let result = true;
    props.group_type_options.forEach(element => {if(element.value.id === form.attribute_group_id && element.value.is_color_group !== 1) {
        form.color = ''
        result = false
    } })
    return result;
});
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiSvg"
                :title="$t('page.attribute.title_edit')"
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
                <div class="relative w-96 mb-6 group required">
                    <div  class="relative z-0 max-w-25 h-6 group">
                        <label  class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.attribute_group.fields.group_type')}}</label>
                    </div>
                    <select v-model="form.attribute_group_id" aria-label="group_id" class="block  w-auto rounded-md border bg-gray-50 border   border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template   v-for="option in props.group_type_options" :key="option.value.id">
                            <option :value="option.value.id">{{option.value.translation[locale].name}}</option>
                        </template>
                    </select>
                </div>
                <div v-for="language in $page.props.languages" :key="language.id">
                    <div class="relative z-0 w-full mb-6 group required" v-show="locale === language.code">
                        <input v-model="form['lang'][language.code]['name']"  type="text" :name="`${language.code}name`" :id="`${language.code}name`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label :for="`${language.code}name`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.attribute.fields.name')}}</label>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.name`]">{{$page.props.errors[`lang.${language.code}.name`]}}</p>
                    </div>
                </div>
                <div v-if="showHideColor" class="relative z-0 w-full mb-6 group  required" v-click-outside="() => {showcolorpicker = false}">
                    <div class="flex" >
                        <input v-model="form.color"  type="text" name="color" id="color" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        <label for="color" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.attribute.fields.color')}}</label>
                        <button type="button"
                                class=" mb-0 p-2 w-8 h-8 border-2 flex self-end mb-1 border-gray-200 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                :style="{ background: form.color }"
                                @click="showcolorpicker = showcolorpicker ? false : true"
                        >
                        </button>

                    </div>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.color">{{$page.props.errors.color}}</p>
                    <div  v-show="showcolorpicker" class="absolute w-48 right-0">
                        <ColorPicker
                            theme="dark"
                            :color="form.color"
                            :colors-default="[]"
                            :sucker-hide="false"
                            :sucker-area="true"
                            @changeColor="changeColor"
                            class="ml-auto position-absolute right-0"
                        />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.edit')}}</button>
                </div>
            </form>
            <BaseLink
                color="gray"
                class="ml-auto"
                small
                :icon="mdiArrowLeftBoldOutline"
                :label="$t('global.back_to_list')"
                :href="`/admin/attribute_group/${parent_route_id}`"
            >
            </BaseLink>
        </SectionMain>
    </LayoutAuthenticated>
</template>
<style>
.color-alpha,
.hu-color-picker .color-show,
.color-type + .color-type{
    display:none;
}
</style>
