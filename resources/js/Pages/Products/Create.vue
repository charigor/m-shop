<script setup>
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionTitle from '@/Components/Partials/SectionTitle.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, ref, onMounted, reactive, computed} from "vue";
import { router,usePage } from '@inertiajs/vue3'
import DraggableVueDropzone from '@/Components/Partials/VueDropzone/DropzoneDraggable.vue'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import CKEditor from '@/Components/Partials/CKEditor/CKEditor.vue';

import Multiselect from '@vueform/multiselect'
import Head from '@/Components/Partials/UI/Head.vue'
import Radio from '@/Components/Partials/UI/Radio.vue'
import Checkbox from '@/Components/Partials/UI/Checkbox.vue'
import Switcher from "@/Components/Partials/Switcher.vue";
import BaseButton from "@/Components/Partials/BaseButton.vue"

import {
    mdiAnimationOutline,
    mdiPlus

} from "@mdi/js";
import debounce from "lodash.debounce";

import {getActiveLanguage,wTrans} from "laravel-vue-i18n";
import {useMainStore} from "@/stores/main";

defineComponent({
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    DraggableVueDropzone,
    SectionMain,
    CKEditor
})
const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Object,
        required: true,
    },
    feature_options: {
        type: Object,
        required: true,
    },
    feature_value_options: {
        type: Object,
        required: true,
    },
    tax_options: {
        type: Object,
        required: true,
    }
});
const pageName = "product";
let locale = ref(useMainStore().lang)
const catOptions = reactive([]);
const urlPrefix = `/admin/${pageName}`;
const activeTab = ref('main')
const activeTabDesc = ref(`short_description_${locale.value}`)
const taxPrice = ref(0);
const form = reactive({
    type: 1,
    brand_id: '',
    active: 0,
    quantity: 0,
    price: 0,
    categories: [],
    image: [],
    main_image: '',
    reference: '',
    width: 0,
    height: 0,
    depth: 0,
    weight: 0,
    features: [],
    tax_id: 0,
    unity: '',
    unit_price_ratio: null,
    lang: {},
});
for(let item in usePage().props.languages){
    form['lang'][usePage().props.languages[item]['code']] = {
        name : '',
        short_description:'',
        link_rewrite: '',
        description:'',
        meta_title: '',
        meta_description: '',
        meta_keywords: '',
    }
}
const unityPrice = computed(() => {
    return form.unit_price_ratio ? form.price / form.unit_price_ratio : null;
})
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
}
const fetchCategories = async (query = null,value) => {
    let response = await axios.get('/admin/select',{params:{param: 'categories',term: query,value : value}})
    return response.data;
}
const fetchBrands = async (query = null,value) => {
    let response = await axios.get('/admin/select',{params:{param: 'brands',term: query,value : value}})
    return response.data;
}
const tabPart = function (name){
    activeTab.value = name;
}

const tabDesc = function (name){
    activeTabDesc.value = `${name}_${locale.value}`;
}
const tab = function (name){
    locale.value = name;
    activeTabDesc.value = activeTabDesc.value.substring(0,activeTabDesc.value.length-2)+name
}
const changeSlug = debounce(async (lang) => {
    let response =  await  axios.post(`/admin/${pageName}/slug`, {'name': form['lang'][lang]['name'] })
    form['lang'][lang]['link_rewrite'] = response.data.slug
},200);
const removeFeature = (object) => {
  form.features = form.features.filter(i => i !== object);
}
const setRatio = (e) => {
    form.unit_price_ratio = (form.price / e.target.value).toFixed(6);
}

const inputPrice = (e) =>{
    if(e.target.value.length) {
        let stage = (form.tax_id ? props.tax_options.filter((i) => Number(i.id) === Number(form.tax_id)).map((i) => i.value) : 0)
        taxPrice.value = Number(e.target.value) + Number(e.target.value / 100 * Number(stage))
    }
}
const inputTaxPrice = (e) =>{
    if(e.target.value.length){
        let stage = (form.tax_id ? props.tax_options.filter((i) => Number(i.id) === Number(form.tax_id)).map((i) =>  i.value) : 0)
        form.price = (Number(e.target.value) /Number((1 + (Number(stage)/100)))).toFixed(6)
    }
}
const changeTax = (e) => {
    let stage = (e ? props.tax_options.filter((i) => Number(i.id) === Number(e)).map((i) => i.value) : 0)
    taxPrice.value = Number(form.price) + Number(form.price / 100 * Number(stage))
    form.price = (Number(taxPrice.value) / Number((1 + (Number(stage) / 100)))).toFixed(6)

}
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAnimationOutline"
                :title="$t(`page.${pageName}.title_create`)"
                main
            >
            </SectionTitleLineWithButton>
            {{$page.props.errors}}
            <div class="flex justify-between">
                <ul class="flex flex-wrap  justify-start text-sm font-medium text-center mb-1"  role="tablist">
                    <li role="product">
                        <button type="button" @click="tabPart('main')" :class="{ 'bg-gray-300 dark:bg-blue-600': activeTab === 'main'}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                            {{$t(`page.${pageName}.tabs.main`)}}
                        </button>
                        <button type="button" @click="tabPart('price')" :class="{ 'bg-gray-300 dark:bg-blue-600': activeTab === 'price'}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                            {{$t(`page.${pageName}.tabs.price`)}}
                        </button>
                        <button type="button" @click="tabPart('delivery')" :class="{ 'bg-gray-300 dark:bg-blue-600': activeTab === 'delivery'}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                            {{$t(`page.${pageName}.tabs.delivery`)}}
                        </button>
                        <button type="button" @click="tabPart('seo')" :class="{ 'bg-gray-300 dark:bg-blue-600': activeTab === 'seo'}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                            {{$t(`page.${pageName}.tabs.seo`)}}
                        </button>
                    </li>
                </ul>
                <ul class="flex flex-wrap  justify-end text-sm font-medium text-center mb-1"  role="tablist">
                    <li role="lang" v-for="item in [...$page.props.languages]">
                        <button type="button" @click="tab(item.code)" :class="{ 'bg-gray-300 dark:bg-blue-600': locale === item.code}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                            {{item.code}}
                        </button>
                    </li>
                </ul>
            </div>
            <form @submit.prevent="submit()" class="mb-4 p-8 border border-gray-200 dark:border-gray-700">
                <Switcher :value="form.active" :topLabel="$t(`page.${pageName}.fields.active`)" name="active" @onChange="(e) => form.active = e" :valueA="0" :valueB="1" :labelA="$t('global.no')"  :labelB="$t('global.yes')"/>
                <div class="grid grid-cols-4 gap-10">

                    <template v-if="activeTab === 'main'">
                        <div class="col-span-3" >
                            <div v-for="language in $page.props.languages" :key="language.id">
                                <div class="relative z-0 w-full mb-6 group required" v-show="locale === language.code">
                                    <input v-model="form['lang'][language.code]['name']" @input="changeSlug(language.code)" type="text" :name="`${language.code}name`" :id="`${language.code}name`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <label :for="`${language.code}name`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.name`)}}</label>
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.name`]">{{$page.props.errors[`lang.${language.code}.name`]}}</p>
                                </div>
                            </div>
                            <div class="relative w-full mb-7 z-10 group">
                                <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{$t(`page.${pageName}.fields.image`)}}</label>
                                <div class="relative row mb-6 group pt-7">
                                    <DraggableVueDropzone :w="Number(130)" :h="Number(115)" @updatePosition="(files) => form.image = files" :main_image="form.main_image"  @mainImage="(file) => form.main_image = file" :maxFiles="Number(15)"  multiple="1"  @removeImage="(file) => form.image = form.image.filter((item) => item !== file)" @loadImages="(file) => form.image.push(file)" :files="[]"></DraggableVueDropzone>
                                </div>
                            </div>
                            <div v-for="language in $page.props.languages" :key="language.id">
                                <div class="relative w-full z-10 group" v-show="locale === language.code">
                                    <ul class="flex flex-wrap mb-5 justify-start text-sm font-medium text-center mb-1"  role="tablist">
                                        <li>
                                            <button type="button" @click="tabDesc(`short_description`)" :class="{ 'bg-gray-300 dark:bg-gray-600': activeTabDesc === `short_description_${language.code}`}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                                                {{$t(`page.${pageName}.fields.short_description`)}}
                                            </button>
                                            <button type="button" @click="tabDesc(`description`)" :class="{ 'bg-gray-300 dark:bg-gray-600': activeTabDesc === `description_${language.code}`}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
                                                {{$t(`page.${pageName}.fields.description`)}}
                                            </button>
                                        </li>
                                    </ul>
                                    <template  v-for="language in $page.props.languages" :key="language.id" >
                                        <div class="relative z-0 w-full mb-6 pt-7 group" v-if="activeTabDesc === `short_description_${language.code}`">
                                            <CKEditor v-model="form['lang'][language.code]['short_description']" :lang="language.code"  :csrf="$page.props.csrf_token"/>
                                        </div>
                                        <div class="relative z-0 w-full mb-6 pt-7 group" v-if="activeTabDesc === `description_${language.code}`">
                                            <CKEditor v-model="form['lang'][language.code]['description']" :lang="language.code"  :csrf="$page.props.csrf_token"/>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <Head size="5" class="mb-5" :content="$t(`page.${pageName}.fields.brand`)"/>
                            <Multiselect  class="multiselect-dark-theme mb-8"
                                          mode="single"
                                          label="label"
                                          :searchable="true"
                                          multiple="false"
                                          :min-chars="1"
                                          :object="false"
                                          :allow-absent="true"
                                          :filter-results="true"
                                          :resolve-on-load="true"
                                          v-model="form.brand_id"
                                          :close-on-select="true"
                                          :options="async function(query) {
                                            return await fetchBrands(query)
                                          }"
                            >
                            </Multiselect>
                            <div >
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.blocks.features.title`)"/>
                                <div class="grid grid-cols-3 gap-10 mb-4" v-for="(item,index) in form.features" :key="index">
                                    <div class="col-span-1">
                                        <div class="mb-2">{{$t(`page.${pageName}.blocks.features.fields.feature`)}}</div>
                                        <Multiselect  class="multiselect-dark-theme mb-3"
                                                      mode="single"
                                                      label="label"
                                                      :searchable="true"
                                                      multiple="false"
                                                      :object="false"
                                                      :allow-absent="true"
                                                      :filter-results="true"
                                                      :resolve-on-load="true"
                                                      v-model="form.features[index]['feature_id']"
                                                      @clear="form.features[index]['feature_value_id'] = null"
                                                      :close-on-select="true"
                                                      :options="props.feature_options">
                                        </Multiselect>
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`features.${index}.feature_id`]">
                                            {{ $page.props.errors[`features.${index}.feature_id`] }}
                                        </p>
                                    </div>
                                    <div class="col-span-1">
                                        <div class="mb-2">{{$t(`page.${pageName}.blocks.features.fields.feature_value`)}}</div>
                                        <Multiselect  class="multiselect-dark-theme mb-3"
                                                      mode="single"
                                                      label="label"
                                                      :searchable="true"
                                                      multiple="false"
                                                      :min-chars="1"
                                                      :object="false"
                                                      :resolve-on-load="true"
                                                      :allow-absent="true"
                                                      :filter-results="true"
                                                      :canClear="true"
                                                      :disabled="!form.features[index]['feature_id']"
                                                      v-model="form.features[index]['feature_value_id']"
                                                      :close-on-select="true"
                                                      :options="props.feature_value_options.filter(i => i.parent === form.features[index]['feature_id'])">
                                        </Multiselect>
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`features.${index}.feature_value_id`]">
                                            {{ $page.props.errors[`features.${index}.feature_value_id`] }}
                                        </p>
                                    </div>
                                    <div class="mt-10">
                                        <BaseButton
                                            color="transparent -ml-7"
                                            class="border-0 "
                                            :icon="mdiTrashCan"
                                            :iconSize="20"
                                            @click="removeFeature(form.features[index])"
                                        />
                                    </div>
                                </div>
                                <BaseButton
                                    color="info"
                                    :icon="mdiPlus"
                                    small
                                    :label="$t(`page.${pageName}.blocks.features.add`)"
                                    @click="form.features.push({feature_id: null,feature_value_id: null})"
                                />
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="p-3">
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.combo.title`)"/>
                                <Radio :label="$t(`page.${pageName}.combo.simple_product`)" value="1" v-model="form.type"/>
                                <Radio :label="$t(`page.${pageName}.combo.combo_product`)" value="2" v-model="form.type"/>
                            </div>
                            <div class="p-3">
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.blocks.reference.title`)"/>
                                <div class="relative z-0 w-full mb-6 group required">
                                    <input v-model="form.reference" type="text"  id="reference" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.reference">{{$page.props.errors.reference}}</p>
                                </div>
                            </div>
                            <div class="p-3">
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.blocks.price.title`)"/>
                                <div class="relative z-1 w-full mb-6 group">
                                    <input v-model="form.price" @keypress="inputPrice"  type="number" step="any" min="0" id="price_main"  class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.price">{{$page.props.errors.price}}</p>
                                    <input v-model="taxPrice" @keypress="inputTaxPrice" type="number" step="any" min="0" id="price_tax"  class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <Multiselect  class="multiselect-dark-theme mb-8  z-10"
                                                  mode="single"
                                                  label="name"
                                                  valueProp="id"
                                                  multiple="false"
                                                  :object="false"
                                                  v-model="form.tax_id"
                                                  :close-on-select="true"
                                                  @change="changeTax"
                                                  :options="props.tax_options"
                                    >
                                    </Multiselect>
                                </div>
                            </div>
                            <div class="p-3">
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.fields.quantity`)"/>
                                <div class="relative z-0 w-full mb-6 group required">
                                    <input v-model="form.quantity" type="number" min="0" id="quantity_main" class="block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.quantity">{{$page.props.errors.quantity}}</p>
                                </div>
                            </div>
                            <div class="p-3">
                                <Head size="5" class="mb-5" :content="$t(`page.${pageName}.blocks.categories.title`)"/>
                                <Multiselect  class="multiselect-dark-theme"
                                              mode="tags"
                                              label="label"
                                              :searchable="true"
                                              :min-chars="1"
                                              :object="false"
                                              :allow-absent="true"
                                              :filter-results="true"
                                              :resolve-on-load="true"
                                              v-model="form.categories"
                                              :close-on-select="false"
                                              :options="async function(query) {
                                            return await fetchCategories(query,form.categories) // check JS block in JSFiddle for implementation
                                          }"
                                >

                                </Multiselect>
                                <template   v-for="category in props.categories.data" :key="category.id">
                                    <Checkbox :style="{'margin-left': `${1 * category.depth}rem`}"  :label="category.translation[locale].title" v-model="form.categories" :value="category.id" />
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-if="activeTab === 'price'">
                        <div class="col-span-4">
                            <div class="grid grid-cols-5 gap-10">
                                <div class="col-span-1">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <input v-model="form.price" @keypress="inputPrice" type="number" step="any" id="tab_price" class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.price">{{$page.props.errors.price}}</p>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <input v-model="taxPrice" @keypress="inputTaxPrice" type="number" step="any" id="tab_price_tax" class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <Multiselect  class="multiselect-dark-theme mb-8  z-10"
                                                      mode="single"
                                                      label="name"
                                                      valueProp="id"
                                                      multiple="false"
                                                      :object="false"
                                                      v-model="form.tax_id"
                                                      :close-on-select="true"
                                                      @change="changeTax"
                                                      :options="props.tax_options"
                                        >
                                        </Multiselect>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-5 gap-10">

                                <div class="col-span-1">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <input v-model="unityPrice" @input="setRatio" type="number" step="0.1" min="0" id="unit_ratio" class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="relative z-0 w-full mb-6 group">
                                        <input v-model="form.unity" type="text" id="unity" class="mb-2 block indent-2 py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.unity">{{$page.props.errors.unity}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-if="activeTab === 'delivery'">
                        <div class="col-span-4">
                            <div class="relative z-0 w-full mb-6 group">
                                <input v-model="form.width"  type="text"  id="width" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label for="width" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.width`)}}</label>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.width">{{$page.props.errors.width}}</p>
                            </div>
                            <div class="relative z-0 w-full mb-6 group">
                                <input v-model="form.height"  type="text"  id="height" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label for="height" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.height`)}}</label>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.height">{{$page.props.errors.height}}</p>
                            </div>
                            <div class="relative z-0 w-full mb-6 group">
                                <input v-model="form.depth"  type="text"  id="depth" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label for="depth" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.depth`)}}</label>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.depth">{{$page.props.errors.depth}}</p>
                            </div>
                            <div class="relative z-0 w-full mb-6 group">
                                <input v-model="form.weight"  type="text"  id="weight" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label for="weight" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.weight`)}}</label>
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.weight">{{$page.props.errors.weight}}</p>
                            </div>
                        </div>
                    </template>
                    <template v-if="activeTab === 'seo'">
                        <div class="col-span-4">
                            <div v-for="language in $page.props.languages" :key="language.id">
                                <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                                    <input v-model="form['lang'][language.code]['link_rewrite']"  type="text" :name="`${language.code}link_rewrite`" :id="`${language.code}link_rewrite`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <label :for="`${language.code}link_rewrite`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.link_rewrite`)}}</label>
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.link_rewrite`]">{{$page.props.errors[`lang.${language.code}.link_rewrite`]}}</p>
                                </div>
                                <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                                    <input v-model="form['lang'][language.code]['meta_title']"  type="text" :name="`${language.code}meta_title`" :id="`${language.code}meta_title`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <label :for="`${language.code}meta_title`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.meta_title`)}}</label>
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_title`]">{{$page.props.errors[`lang.${language.code}.meta_title`]}}</p>
                                </div>
                                <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                                    <input v-model="form['lang'][language.code]['meta_description']"  type="text" :name="`${language.code}meta_description`" :id="`${language.code}meta_description`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <label :for="`${language.code}meta_description`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.meta_description`)}}</label>
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_description`]">{{$page.props.errors[`lang.${language.code}.meta_description`]}}</p>
                                </div>
                                <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">
                                    <input v-model="form['lang'][language.code]['meta_keywords']"  type="text" :name="`${language.code}meta_keywords`" :id="`${language.code}meta_keywords`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                    <label :for="`${language.code}meta_keywords`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t(`page.${pageName}.fields.meta_keywords`)}}</label>
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors[`lang.${language.code}.meta_keywords`]">{{$page.props.errors[`lang.${language.code}.meta_keywords`]}}</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>

<style>

</style>
