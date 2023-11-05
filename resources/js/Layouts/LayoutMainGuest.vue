<script setup>
import { useStyleStore } from "@/stores/style.js";

const styleStore = useStyleStore();
</script>

<template>
  <div  :class="{ dark: styleStore.darkMode }">
    <div class="bg-main-light text-main-dark dark:bg-main-dark  dark:text-main-light">
      <slot />
    </div>
  </div>
</template>
