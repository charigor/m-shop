<script setup>

import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, reactive, watch,ref} from "vue";
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/Partials/Dropzone/Dropzone.vue'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import FilePondPluginImagePreview from "filepond-plugin-image-preview/dist/filepond-plugin-image-preview";
import FilePondPluginFilePoster from 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster';
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import vueFilePond  from 'vue-filepond';
import CKEditor from '@/Components/Partials/CKEditor/CKEditor.vue';
const FilePond = vueFilePond(FilePondPluginImagePreview,FilePondPluginFilePoster);
import {
    mdiAccountBoxMultipleOutline

} from "@mdi/js";
import debounce from "lodash.debounce";
import pickBy from "lodash/pickBy";

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
let form = reactive({
    description: props.category.description,
    title: props.category.title,
    link_rewrite: props.category.link_rewrite,
    parent: null,
    image: []
});
const urlPrefix = '/admin/categories';
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
}
defineComponent({
    SectionTitleLineWithButton,
    SectionMain,
    CKEditor,
    Dropzone
})
function handleFilePondLoad(response){
    let result = JSON.parse(response)
    form.image.push({'image': result.path});
    return result;
}
function handleFilePondRevert(res,load){

    form.image = form.image.filter((image) => image !== res.path)
    router.delete('/admin/revert/' + res.folder)
    load();
}
const changeSlug = debounce(async () => {
    let response =  await  axios.post('/admin/categories/slug', {'title': form.title })
    form.link_rewrite = response.data.slug
},500);

</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.category.title_create')"
                main
            >
            </SectionTitleLineWithButton>
            <form @submit.prevent="submit()">
<!--                <div class="relative z-0  mb-5 group row " style="width:300px">-->
<!--                    <div class="justify-center row">-->
<!--                        <div style="height: 15px" class="relative z-0 w-full group">-->
<!--                            <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Category Poster</label>-->
<!--                        </div>-->
<!--                        <file-pond-->
<!--                            id="image"-->
<!--                            name="image"-->
<!--                            ref="category"-->
<!--                            class="col-4"-->
<!--                            class-name="my-pond"-->
<!--                            label-idle="Drop files here..."-->
<!--                            maxFiles="1"-->
<!--                            accepted-file-types="image/jpeg, image/png"-->
<!--                            :files="form.image"-->
<!--                            @init="handleFilePondInit"-->
<!--                            :server="{-->
<!--                                url:'',-->
<!--                                process: {-->
<!--                                    url: '/admin/upload',-->
<!--                                    method: 'Post',-->
<!--                                    onload: handleFilePondLoad-->
<!--                                },-->
<!--                                revert: handleFilePondRevert,-->
<!--                                headers: {-->
<!--                                    'X-CSRF-TOKEN': $page.props.csrf_token-->
<!--                                }-->

<!--                        }"-->

<!--                        />-->
<!--                    </div>-->
<!--                </div>-->

                <div class="relative z-0 w-full mb-6 group required">
                    <input v-model="form.title" @input="changeSlug" type="text" name="title" id="title" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="title" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.title')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.title">{{$page.props.errors.title}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group required">
                    <input v-model="form.link_rewrite" type="text" name="link_rewrite" id="link_rewrite" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="link_rewrite" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.link_rewrite')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.link_rewrite">{{$page.props.errors.link_rewrite}}</p>
                </div>
                <div class="z-0 w-full mb-6">
                    <div style="height: 2px" class="relative z-0 w-full group required">
                        <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.category.fields.parent')}}</label>
                    </div>
                    <v-select :options="[{'id': 0, name: $t('page.category.no_parents')},...props.categories]" :reduce="categories => categories.id" v-model="form.parent" label="name" class="mt-3 py-2"></v-select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.parent">{{$page.props.errors.roles}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <CKEditor v-model="form.description" :csrf="$page.props.csrf_token"/>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <Dropzone @removeImage="(file) => form.image = form.image.filter((item) => item != file)" @loadImages="(file) => form.image.push(file)" path="https://m-shop.loc/admin/categories/storeMedia" :files="[]" :csrf="$page.props.csrf_token"/>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>
