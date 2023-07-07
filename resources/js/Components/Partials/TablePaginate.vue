
<script>
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref, onMounted } from "vue";

export default {
    components: {
        Link,
    },
    props: {
        data: Object,
    },
    methods: {
        p(p){
            const urlParams =  (new URL(p)).searchParams

            const pageParam = urlParams.get('page');
            this.$emit('link',pageParam)
        }
    }
}
</script>

<template>
    <div v-if="data?.links.length > 3">
        <div class="flex flex-wrap mb-1" >
            <template v-for="(item, key) in data.links" :key="key">
                <div v-if="item.url === null" :key="key" class="mb-1 mr-1 px-4 py-3  text-sm leading-4 border rounded" v-html="!Number.isNaN(parseInt(item.label)) ? item.label : $t(`global.pagination.${item.label}`)"></div>
                <button v-else :key="`link-${key}`" class="mb-1 mr-1 px-4 py-3  text-sm leading-4 dark:hover:text-state-400   dark:hover:bg-gray-700 dark:focus:bg-gray-700 border rounded hover:bg-gray-100" :class="{ 'dark:bg-gray-600 || bg-gray-200': item.active }" @click="p(item.url)" v-html="!Number.isNaN(parseInt(item.label)) ? item.label : $t(`global.pagination.${item.label}`)"></button>
            </template>
        </div>
    </div>
</template>

