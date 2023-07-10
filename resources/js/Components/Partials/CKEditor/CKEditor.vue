<template>
    <div class="fg-black" style="--ck-border-radius: 0.25;">
        <CKEditor  :disabled="editorDisabled"  :editor="editor" v-model="editorData" :config="editorConfig" />
    </div>
</template>

<script setup>
import {reactive, ref, defineProps, watch,defineEmits} from "vue";

import Editor from "@/editor.js";
import {component as CKEditor} from "@ckeditor/ckeditor5-vue";

const props = defineProps({
    modelValue: String,
    csrf: String
})
const emit = defineEmits(['update:modelValue'])
const editor = reactive(Editor);
const editorData = ref(props.modelValue || '');


const editorConfig =  ref({
    heading: {
        options: [
            {model: 'paragraph',title: 'Paragraph',class: 'ck-heading_paragraph'}
        ]
    },
    simpleUpload: {
        // The URL that the images are uploaded to.
        uploadUrl: 'https://m-shop.loc/admin/uploadEditorImage',

        // Enable the XMLHttpRequest.withCredentials property.
        withCredentials: false,

        // Headers sent along with the XMLHttpRequest to the upload server.
        headers: {
            'X-CSRF-TOKEN': props.csrf,
        }
    }
})
const editorDisabled = ref(false)
watch(editorData,() => {
    emit('update:modelValue',editorData.value)
})
</script>

<style>
    .ck-editor__editable {
        min-height: 200px;
    }
    .dark .ck.ck-editor__main>.ck-editor__editable , .dark .ck.ck-toolbar {
        background:  rgb(30 41 59 / var(--tw-bg-opacity));
    }
    .dark .ck.ck-toolbar button{
        color: white
    }
    .dark .ck.ck-toolbar .ck-button:hover,.dark .ck.ck-toolbar .ck-button.ck-on:hover{
        background-color: rgb(30 41 59 / var(--tw-bg-opacity))
    }
    .dark .ck.ck-toolbar .ck-button.ck-on{
        background-color: rgb(15 23 42 / var(--tw-bg-opacity))
    }
    .dark .ck-dropdown__panel,.dark .ck-dropdown__panel:hover {
        background:  rgb(30 41 59 / var(--tw-bg-opacity));
    }
</style>
