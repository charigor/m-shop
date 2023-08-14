<script setup>
import {defineEmits,computed, defineProps, reactive, watch} from 'vue';
import { useDropzone } from 'vue3-dropzone';
import {
    mdiPlusCircleOutline
} from "@mdi/js";

const props = defineProps({
    path: String,
    csrf: String,
    files: Array,
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
watch(state, () => {
    console.log('state', state);
});

watch(isDragActive, () => {
    console.log('isDragActive', isDragActive.value, rest);
});
const url = "/admin/categories/storeMedia"; // Your url on the server side
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
          state.files.push(response.data)
          emit('loadImages', response.data.name)
      }

    }

    // post the formData to your backend where storage is processed. In the backend, you will need to loop through the array and save each file through the loop.


};
async function onDrop(acceptFiles, rejectReasons) {

    saveFiles(acceptFiles);
}
const emit = defineEmits(['loadImages','removeImage'])
function handleClickDeleteFile(index,file) {
    state.files.splice(index, 1);

    emit('removeImage', file.file_name !== undefined ? file.file_name: file.name )
}
</script>

<template>
    <div>
    <div class="dark:bg-transparent flex flex-col-reverse" v-if="props.viewType === 'fakeInput' && props.maxFiles === 1">
        <div class="dropzone dark:bg-transparent border-r-2 border-gray-200 dark:border-gray-600" :disabled="disabled" v-bind="getRootProps()">
        <div class=" dark:bg-transparent border-gray-200 dark:border-gray-600 w-full border-2 text-left"
             :class="{isDragActive}">
            <button class="dz-button" type="button">
                <span class="p-2 border-r-2 border-gray-200 dark:border-gray-600 inline-block">{{$t('global.choose_file')}}</span>
                <span class="fake-input-label ml-2 text-sm"> {{state.files.length ? (state.files[0]?.file_name ?? state.files[0]?.name) : $t('global.no_file_chosen')}}</span>
            </button>
            <input v-bind="getInputProps()" />
        </div>
       </div>
        <div class="prev-container files mb-4 flex-wrap" v-if="state.files.length > 0">
            <div class="file-item"  v-for="(file, index) in state.files" :key="index">
                <span class="bg-indigo-300  overflow-hidden mb-1" :style="`width: ${props.w}px;height: ${props.h}px;`">
                    <img class="object-cover m-auto h-full" :src="file.preview_url !== undefined ? file.preview_url : '/storage/tmp/uploads/'+file.name" :alt="file.name">
                </span>
                <div class="w-full text-center border-gray-600">
                    <button type="button" class="w-full delete-file" @click="handleClickDeleteFile(index,file)"
                    >{{$t('global.remove_file')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="(props.viewType === 'square')" class="dark:bg-transparent flex flex-col" >
        <div class="dropzone dark:bg-transparent border-r-2 border-gray-200 dark:border-gray-600 ml-1" :disabled="disabled" v-bind="getRootProps()" :style="`width: ${props.w}px; height: ${props.h}px;`">
            <div :class="{isDragActive}" class="border-0 dark:bg-transparent h-full flex justify-center items-center">
                <input v-bind="getInputProps()" />
                <svg
                    viewBox="0 0 24 24"
                    width="100px"
                    height="100px"
                    class="inline-block m-auto align-self-center text-gray-500"
                >
                    <path fill="#1d4ed8" :d="mdiPlusCircleOutline" />
                </svg>
            </div>
        </div>
        <div class="prev-container files flex-wrap" v-if="state.files.length > 0">
            <div class="file-item"  v-for="(file, index) in state.files" :key="index">
                <span class="bg-indigo-300  overflow-hidden mb-1" :style="`width: ${props.w}px;height: ${props.h}px;`">
                    <img class="object-cover m-auto h-full" :src="file.preview_url !== undefined ? file.preview_url : '/storage/tmp/uploads/'+file.name" :alt="file.name">
                </span>
                <div class="w-full text-center border-gray-600">
                    <button type="button" class="w-full delete-file" @click="handleClickDeleteFile(index,file)">{{$t('global.remove_file')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="border dark:bg-transparent" :class="{isDragActive}" >
        <div class="dropzone dark:bg-transparent border-r-2 border-gray-200 dark:border-gray-600" :disabled="disabled" v-bind="getRootProps()">
            <input v-bind="getInputProps()" />
            <p v-if="isDragActive">Drop the files here ...</p>
            <p v-else>Drag and drop files here, or Click to select files</p>
        </div>
        <div class="prev-container files mb-4 flex-wrap" v-if="state.files.length > 0">
            <div class="file-item"  v-for="(file, index) in state.files" :key="index">
                <span class="bg-indigo-300  overflow-hidden mb-1" :style="`width: ${props.w}px;height: ${props.h}px;`">
                    <img class="object-cover m-auto h-full" :src="file.preview_url !== undefined ? file.preview_url : '/storage/tmp/uploads/'+file.name" :alt="file.name">
                </span>
                <div class="w-full text-center border-gray-600">
                    <button type="button" class="w-full delete-file" @click="handleClickDeleteFile(index,file)"
                    >{{$t('global.remove_file')}}</button>
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
.prev-container{
    display: flex;
    justify-content: flex-start;
}
</style>

