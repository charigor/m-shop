<script setup>
import { useStyleStore } from "@/stores/style.js";

const styleStore = useStyleStore();
</script>

<template>
  <div class="bg-main-light-800">
    <div class="bg-main-light-800">
      <slot />
    </div>
  </div>
</template>
