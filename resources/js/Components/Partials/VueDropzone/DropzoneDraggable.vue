<script setup>
import {defineEmits,computed,onMounted, defineProps,ref, reactive, watch} from 'vue';
import BaseIcon from "@/Components/Partials/BaseIcon.vue"
import { useDropzone } from 'vue3-dropzone';
import {
    mdiPlusCircleOutline,
    mdiDotsGrid
} from "@mdi/js";
import draggable from 'vuedraggable'
const props = defineProps({
    path: String,
    csrf: String,
    files: Array,
    main_image: String,
    acceptedFiles: {
        type:  String,
        default: ".jpeg,.jpg,.png,.gif"
    },
    maxFiles: {
        type: Number,
        default: 1
    },
    viewType:{
        type: String,
        default: 'default'
    },
    multiple:{
        type: Boolean,
        default: false
    },
    w: {
        type: Number,
        default: 120
    },
    h: {
        type: Number,
        default: 120
    },

})
console.log(props.files);
const state = reactive({
    files: props.files,
});

const disabled = computed (() => state.files.length >= props.maxFiles)

const { getRootProps, getInputProps, isDragActive, ...rest } = useDropzone({
    maxFiles: props.maxFiles,
    accept: props.acceptedFiles,
    multiple: props.multiple,
    disabled: disabled,
    onDrop,

});
const url = "/admin/product/storeMedia"; // Your url on the server side
 const saveFiles = async (files) => {
     // pass data as a form
    for (var x = 0; x < files.length; x++) {
        const formData = new FormData();
        // append files as array to the form, feel free to change the array name
        formData.append("file", files[x]);

      let response =   await axios
            .post(url, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            })
      if(response){
          if(x === 0 && !state.files.length){
              checkedMain.value = response.data.name
              emit('mainImage', response.data.name)
          }
          state.files.push(response.data)
          emit('loadImages', response.data.name)
      }

    }

    // post the formData to your backend where storage is processed. In the backend, you will need to loop through the array and save each file through the loop.


};
async function onDrop(acceptFiles, rejectReasons) {
    saveFiles(acceptFiles);
}
const emit = defineEmits(['loadImages','removeImage','mainImage','updatePosition'])
function handleClickDeleteFile(file) {

    const index =  state.files.indexOf(file)
    state.files.splice(index,1);

    emit('removeImage', getImageFileName(file) )
}
const sorting = () => {
    emit('updatePosition',  state.files.map((i) => {
        return getImageFileName(i)

    }))
}
//Modal actions

const showModal = ref(null)
const checkedMain = ref(null)
const prevMain = ref(null);
const checkedMainEvent = function ()  {
 emit('mainImage', checkedMain )
}


const triggerModal = (el) => {
    showModal.value = !showModal.value;
    prevMain.value = el;
}
onMounted(() => {
   checkedMain.value = props.main_image
});
const getImageFileName = (file) => {
    return file.hasOwnProperty('collection_name') ? file.file_name : file.name
}
</script>

<template>
    <div>
    <div  class="border dark:bg-transparent" :class="{isDragActive}" >



        <div class="prev-container files mb-4  relative" v-click-outside="() => {showModal = false}">
            <div class="dropzone dark:bg-transparent mt-4 block float-left border-r-2 py-1 cursor-pointer px-2 mr-5 border-gray-200 hover:dark:border-gray-400 hover:border-gray-400 dark:border-gray-600" :disabled="disabled" v-bind="getRootProps()" :style="`width: ${props.w+5}px; height: ${props.h}px;`">
                <div :class="{isDragActive}" class="border-0 dark:bg-transparent h-full flex justify-center items-center">
                    <input v-bind="getInputProps()" />
                    <svg
                        viewBox="0 0 24 24"
                        width="100px"
                        height="100px"
                        class="inline-block fill-black m-auto align-self-center text-gray-500"
                    >
                        <path fill="#e5e7eb" :d="mdiPlusCircleOutline" />
                    </svg>
                </div>
            </div>
            <draggable class="block" @end="sorting" v-model="state.files"  tag="div" handle=".handle"   v-if="state.files.length">
                <template #item="{element}" >
                    <div class="file-item relative inline-flex float-left border-2 mr-2 hover:dark:border-red cursor-pointer">
                        <div class="bg-indigo-300  overflow-hidden mb-1" :style="`width: ${props.w}px;height: ${props.h}px;`" >
                         <div class="m-1 bg-gray-700 left:0 top:0 absolute invisible group-hover:visible">
                            <BaseIcon type="mdi" size="30" middle style="vertical-align: middle" class="handle text-white inline-block delay-75  cursor-move  " :path="mdiDotsGrid"></BaseIcon>
                        </div>
                            <div class="z-10 bg-gray-700 py-1 px-2 w-100 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 " v-if="getImageFileName(element) === checkedMain">
                               <span class="whitespace-nowrap text-white">{{$t('global.main_image')}}</span>
                            </div>
                            <img   @click="triggerModal(element)" class="object-cover m-auto h-full" :src="element.preview_url !== undefined ? element.preview_url : '/storage/tmp/uploads/'+element.name" :alt="element.name">
                        </div>
                        <div class="w-full text-center border-gray-600">
                            <button type="button" class="w-full delete-file" @click="handleClickDeleteFile(element)" >{{$t('global.remove_file')}}</button>
                        </div>
                    </div>
                </template>
            </draggable>
            <!-- Main modal -->
            <div  v-if="showModal"  class="absolute w-sm max-w-sm  top-50  ml-auto mt-3 z-50  p-1 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="w-sm max-w-sm max-h-full" >
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700" >
                        <button type="button" @click="showModal = false" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                        <div class="px-5 py-3">
                            <h5 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{$t('global.set_main_image')}}</h5>
                            <div v-for="(item,index) in state.files">
                                <div class="flex mb-2 items-center" v-if="prevMain === item">
                                    <input :id="`ch${index}`" :true-value="getImageFileName(item)" :false-value="null" v-model="checkedMain" type="checkbox" name="checkbox"   @change="checkedMainEvent"    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label  :for="`ch${index}`" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{$t('global.main_image')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</template>

<style  scoped>
.dropzone,
.files {
    width: 100%;
    border-radius: 8px;
    font-size: 12px;
    line-height: 1.5;
}

.border {
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    transition: all 0.3s ease;
}
.border.isDragActive {
    border: 2px dashed #ffb300;
}
.file-item {
    border-radius: 8px;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: space-between;
    padding: 7px;
    margin-top: 10px;
}
.file-item .delete-file {
    background: red;
    color: #fff;
    padding: 5px 10px;
    cursor: pointer;
}
</style>

