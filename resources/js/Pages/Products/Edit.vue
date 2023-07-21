<script setup>
import SectionTitleLineWithButton from '@/Components/Partials/SectionTitleLineWithButton.vue'
import SectionMain from '@/Components/Partials/SectionMain.vue'
import {defineComponent, defineProps, ref, reactive} from "vue";
import { router,usePage } from '@inertiajs/vue3'
import DraggableVueDropzone from '@/Components/Partials/VueDropzone/DropzoneDraggable.vue'
import LayoutAuthenticated from "../../Layouts/LayoutAuthenticated.vue";
import CKEditor from '@/Components/Partials/CKEditor/CKEditor.vue';
import TreeMenu from '@/Components/Partials/TreeMenu.vue'
import {
    mdiAnimationOutline

} from "@mdi/js";
import debounce from "lodash.debounce";

import {getActiveLanguage,wTrans} from "laravel-vue-i18n";
import {useMainStore} from "@/stores/main";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    }
});
let locale = ref(useMainStore().lang)

const form = reactive({
    active: props.product.active,
    image: props.product.image.map(i => i.file_name),
    main_image: props.product.image.filter(i => i.custom_properties.main_image === 1)?.map((i) =>   i.file_name )[0]
});

const urlPrefix = '/admin/product';
const submit = () => {
    router.put(`${urlPrefix}/${props.product.id}`, form, {preserveState: true})
}
defineComponent({
    LayoutAuthenticated,
    SectionTitleLineWithButton,
    SectionMain,
    CKEditor,
    TreeMenu
})


</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiAnimationOutline"
                title="Product"
                main
            >
            </SectionTitleLineWithButton>
            <form @submit.prevent="submit()" class="mb-4 p-8 border border-gray-200 dark:border-gray-700">
                <Switcher :value="form.active" :topLabel="$t('page.category.fields.active')" name="cat_active" @onChange="(e) => form.active = e" :valueA="0" :valueB="1" :labelA="$t('global.no')"  :labelB="$t('global.yes')"/>
                <div class="relative w-full mb-7 z-10 group">
                <DraggableVueDropzone :w="Number(130)" :h="Number(115)" @updatePosition="(files) => form.image = files" :main_image="form.main_image"  @mainImage="(file) => form.main_image = file" :maxFiles="Number(6)"  multiple="1"  @removeImage="(file) => form.image = form.image.filter((item) => item !== file)" @loadImages="(file) => form.image.push(file)" path="/admin/product/storeMedia" :files="props.product.image"></DraggableVueDropzone>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{$t('global.save')}}</button>
                </div>
            </form>
        </SectionMain>
    </LayoutAuthenticated>
</template>
