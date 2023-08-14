<template>
    <div class="fg-black" style="--ck-border-radius: 0.25;">
        <CKEditor  :disabled="editorDisabled"  :editor="editor" v-model="editorData" :config="editorConfig" />
    </div>
</template>

<script setup>
import {reactive, ref, defineProps, watch,defineEmits} from "vue";

import Editor from "@/editor.js";
import {getActiveLanguage } from 'laravel-vue-i18n';
import '@ckeditor/ckeditor5-build-classic/build/translations/uk';
import '@ckeditor/ckeditor5-build-classic/build/translations/en-gb';
import '@ckeditor/ckeditor5-build-classic/build/translations/ru';
import {component as CKEditor} from "@ckeditor/ckeditor5-vue";
import {usePage} from "@inertiajs/vue3";
import { useMainStore } from "@/stores/main.js";

const props = defineProps({
    modelValue: String,
    csrf: String,
    lang: String
})
const emit = defineEmits(['update:modelValue'])
const editor = ref(Editor);
const editorData = ref(props.modelValue || '');

const editorConfig =  reactive({
    heading: {
        options: [
            {model: 'paragraph',title: 'Paragraph',class: 'ck-heading_paragraph'}
        ]
    },
    simpleUpload: {
        // The URL that the images are uploaded to.
        uploadUrl: '/admin/uploadEditorImage',

        // Enable the XMLHttpRequest.withCredentials property.
        withCredentials: true,

        // Headers sent along with the XMLHttpRequest to the upload server.
        headers: {
            'X-CSRF-TOKEN': props.csrf,
        },

    },
    language:  props.lang
})

const editorDisabled = ref(false)
watch(editorConfig,() => {
    console.log('d')
})
watch(editorData,() => {
    emit('update:modelValue',editorData.value)
})
</script>

<style>
    .ck-editor__editable {
        min-height: 200px;
    }
    .dark .ck.ck-editor__main>.ck-editor__editable , .dark .ck.ck-toolbar {
        background:  rgb(30 41 59 );
    }
    .dark .ck.ck-toolbar button{
        color: white
    }
    .dark .ck.ck-toolbar .ck-button:hover,.dark .ck.ck-toolbar .ck-button.ck-on:hover{
        background-color: rgb(30 41 59 )
    }
    .dark .ck.ck-toolbar .ck-button.ck-on{
        background-color: rgb(15 23 42 )
    }
    .dark .ck-dropdown__panel,.dark .ck-dropdown__panel:hover {
        background:  rgb(30 41 59);
    }
</style>
