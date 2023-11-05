<script setup>

import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps,reactive, ref} from "vue";
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import { mdiAccountBoxMultipleOutline } from "@mdi/js";
import { router,useForm } from '@inertiajs/vue3'

import FilePondPluginImagePreview from "filepond-plugin-image-preview/dist/filepond-plugin-image-preview";
import FilePondPluginFilePoster from 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster';
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import vueFilePond , { setOptions } from 'vue-filepond';
import vSelect from "vue-select";
const FilePond = vueFilePond(FilePondPluginImagePreview,FilePondPluginFilePoster);


const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
});
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    roles: props.user.roles,
    password: null,
    password_confirmation: null,
    avatar: []

});
const urlPrefix = '/admin/users';
const submit = () => {
    router.post(`${urlPrefix}`, form, {preserveState: true})
}
defineComponent({
    SectionTitleLineWithButton,
    SectionMain
})
// function handleFilePondInit(){
//         form.avatar = [{
//             name: 'poster',
//             source:  'https://m-shop.loc/',
//             options: {
//                 type: 'local',
//                 load: false,
//                 metadata: {
//                     poster:  'https://m-shop.loc/storage/images/tmp/image-648d40ccc75d92.14700311/blue-men-shorts-preview.jpg'
//                 }
//
//             }
//         }]
// }
function handleFilePondLoad(response){
    let result = JSON.parse(response)
    form.avatar.push({'image': result.path});
    return result;
}
function handleFilePondRevert(res,load,error){

    form.avatar = form.avatar.filter((image) => image !== res.path)
    router.delete('/admin/revert/' + res.folder)
    load();
}
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAccountBoxMultipleOutline"
                :title="$t('page.user.title_create')"
                main
            >
            </SectionTitleLineWithButton>
            <form @submit.prevent="submit()">
                <div class="relative z-0  mb-5 group row " style="width:200px">
                    <div class="justify-center row">
                        <div style="height: 15px" class="relative z-0 w-full group">
                            <label class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Avatar</label>
                        </div>
                        <file-pond
                            id="avatar"
                            name="image"
                            ref="pond"
                            class="col-4"
                            class-name="my-pond"
                            :label-idle="`${$t('global.drop_files_here')}...`"
                            maxFiles="1"
                            accepted-file-types="image/jpeg, image/png"
                            :files="form.avatar"
                            @init="handleFilePondInit"
                            :server="{
                                url:'',
                                process: {
                                    url: '/admin/upload',
                                    method: 'Post',
                                    onload: handleFilePondLoad
                                },
                                revert: handleFilePondRevert,
                                headers: {
                                    'X-CSRF-TOKEN': $page.props.csrf_token
                                }

                        }"

                        />
                    </div>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.name" type="text" name="name" id="name" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    <label for="name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.user.fields.name')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.name">{{$page.props.errors.name}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.email" type="text" name="email" id="email" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"  />
                    <label for="email" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.user.fields.email')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.email">{{$page.props.errors.email}}</p>
                </div>
                <div class="z-0 w-full mb-6">
                    <div style="height: 2px" class="relative z-0 w-full group">
                        <label for="email" class="absolute mb-5 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.user.fields.role')}}</label>
                    </div>
                    <v-select multiple :options="props.roles" :reduce="roles => roles.id" v-model="form.roles" label="name" class="mt-3 py-2"></v-select>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.roles">{{$page.props.errors.roles}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.password" type="password" name="password" id="password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.user.fields.password')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.password">{{$page.props.errors.password}}</p>
                </div>
                <div class="relative z-0 w-full mb-6 group">
                    <input v-model="form.password_confirmation" type="password" name="password_confirmation" id="password_confirmation" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                    <label for="password_confirmation" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">{{$t('page.user.fields.confirm_password')}}</label>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="$page.props.errors.password_confirmation">{{$page.props.errors.password_confirmation}}</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>

        </SectionMain>
    </LayoutAuthenticated>
</template>
