<script setup>

import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps,reactive} from "vue";
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
const form = reactive({
    body: props.category.body,
    name: props.category.name,
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
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                title="Categories Create"
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

                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.name" type="text" name="name" id="name" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Name</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.name">{{$page.props.errors.name}}</p>
                </div>
                <div class="z-0 w-full mb-6">
                    <div style="height: 2px" class="relative z-0 w-full group">
                        <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Parent Category</label>
                    </div>
                    <v-select :options="[{'id': 0, name: 'No parents' },...props.categories]" :reduce="categories => categories.id" v-model="form.parent" label="name" class="mt-3 py-2"></v-select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.parent">{{$page.props.errors.roles}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <CKEditor v-model="form.body" :csrf="$page.props.csrf_token"/>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <Dropzone @removeImage="(file) => form.image = form.image.filter((item) => item != file)" @loadImages="(file) => form.image.push(file)" path="https://m-shop.loc/admin/categories/storeMedia" :files="[]" :csrf="$page.props.csrf_token"/>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>
