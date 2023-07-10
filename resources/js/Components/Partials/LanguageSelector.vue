

<script setup>
import { Link, router } from '@inertiajs/vue3';
import BaseLink from "@/Components/Partials/BaseLink.vue"
import BaseButton from "@/Components/Partials/BaseButton.vue"
import { loadLanguageAsync } from 'laravel-vue-i18n';
import Dropdown from "@/Components/Dropdown.vue";
import {
    mdiWeb,
} from "@mdi/js";
import {defineProps,defineComponent} from "vue";



const changeLocale = async function (code){
  const response =  await axios.post(`/admin/language`,{lang: code})
    if(response.status === 200){
        this.loadLanguageAsync(response.data.locale);
        router.reload({preserveState: true,  preserveScroll: true})
    }
}
</script>
<template>
    <div class="block lg:flex items-center relative cursor-pointer text-black dark:text-white dark:hover:text-slate-400 hover:text-blue-500 py-2 px-3 lg:w-16 lg:justify-center">

        <Dropdown width="auto">
            <template  #trigger>
                <BaseButton
                    color="gray"
                    small
                    :icon="mdiWeb"
                    class="border-0"
                >
                </BaseButton>
            </template>
            <template  #content>
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" >
                    <li v-for="item in $page.props.languages"  :key="item.id">
                        <BaseButton
                            color="gray"
                            small
                            :label="item.name"
                            class="border-0"
                            @click="changeLocale(item.code)"
                        >
                        </BaseButton>
                    </li>
                </ul>
            </template>
        </Dropdown>
    </div>
</template>
<style scoped>

</style>
