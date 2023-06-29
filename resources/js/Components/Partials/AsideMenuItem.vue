<script setup>
import {ref, computed, nextTick} from "vue";
 // import { RouterLink } from "vue-router";
import { useStyleStore } from "@/stores/style.js";
import { Link } from "@inertiajs/vue3";
import { mdiMinus, mdiPlus } from "@mdi/js";
import { getButtonColor } from "@/colors";
import BaseIcon from "@/Components/Partials/BaseIcon.vue";
import AsideMenuList from "@/Components/Partials/AsideMenuList.vue";


// Add itemHref
const itemHref = computed(() =>
    props.item.route ? route(props.item.route) : props.item.href
);

// Add activeInactiveStyle
const activeInactiveStyle = computed(() =>
    props.item.route && route().current(props.item.route)
        ? styleStore.asideMenuItemActiveStyle
        : ""
);

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
    roles: {
        required: true,
    },
  isDropdownList: Boolean,
});

const emit = defineEmits(["menu-click"]);

const styleStore = useStyleStore();

const hasColor = computed(() => props.item && props.item.color);

const asideMenuItemActiveStyle = computed(() =>
  hasColor.value ? "" : styleStore.asideMenuItemActiveStyle
);
const isDropdownActive = ref(false);

const componentClass = computed(() => [
  props.isDropdownList ? "py-3 px-6 text-sm" : "py-3",
  hasColor.value
    ? getButtonColor(props.item.color, false, true)
    : `${styleStore.asideMenuItemStyle} `,
]);

const hasDropdown = computed(() => !!props.item.menu);

const menuClick = (event) => {
  emit("menu-click", event, props.item);

  if (hasDropdown.value) {
    isDropdownActive.value = !isDropdownActive.value;
  }
};
const checkPermissions = computed(() => {
    if(props.item.hasOwnProperty('roles')){
        if(props.item.label === 'Logout' || !props.item.roles.length){
            return true
        }else{
            return props.item.roles.some((el) => props.roles.includes(el))
        }
    }
    return true;

});

</script>

<template>
  <li    v-if="checkPermissions">

    <component

      :is="item.route ? Link : 'a'"
      :href="itemHref"
      :target="item.target ?? null"
      class="flex cursor-pointer text-black dark:text-white hover:text-gray-600 hover:dark:text-gray-300"
      :class="componentClass"
      @click="menuClick"
    >
      <BaseIcon
        v-if="item.icon"
        :path="item.icon"
        class="flex-none text-black dark:text-gray-300"
        w="w-16"
        :size="18"
      />
      <span
        class="grow text-ellipsis line-clamp-1 text-black dark:text-gray-300"
        >{{ item.label }}</span
      >
      <BaseIcon
        v-if="hasDropdown"
        :path="isDropdownActive ? mdiMinus : mdiPlus"
        class="flex-none"
        :class="[{ 'pr-12': !hasDropdown }, activeInactiveStyle]"
        w="w-12"
      />
    </component>
    <AsideMenuList
      v-if="hasDropdown"
      :menu="item.menu"
      :class="[
        styleStore.asideMenuDropdownStyle,
        isDropdownActive ? 'block dark:bg-slate-800/50' : 'hidden',
      ]"
      is-dropdown-list
    />
  </li>
</template>
