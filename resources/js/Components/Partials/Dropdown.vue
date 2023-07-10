<script setup>
import { mdiChevronUp, mdiChevronDown } from "@mdi/js";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { useStyleStore } from "@/stores/style.js";
import BaseButton  from "@/Components/Partials/BaseButton.vue";
// import { useMainStore } from "@/stores/main.js";

import BaseIcon from "@/Components/Partials/BaseIcon.vue";
import UserAvatarCurrentUser from "@/Components/Partials/UserAvatarCurrentUser.vue";
import NavBarMenuList from "@/Components/Partials/NavBarMenuList.vue";
import BaseDivider from "@/Components/Partials/BaseDivider.vue";


const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
    icon: {
        required: true,
    }
});

</script>

<template>

    <div>
        <BaseButton
            :icon="props.icon"
            middle
            color="gray"
            data-dropdown-toggle="dropdown"
            @click="reset"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center   "
        />
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center   " type="button" :path="props.icon"> <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
        </button>
    <!-- Dropdown menu -->
        <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                <li v-for="item in props.items"  :key="item.id">
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{item.name}}</a>
                </li>
            </ul>
        </div>
    </div>
</template>

