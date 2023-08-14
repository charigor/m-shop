<script setup>
import { computed, ref, onMounted, reactive } from "vue";
import { Head,router } from "@inertiajs/vue3";
 import { useMainStore } from "@/stores/main";
import {

} from "@mdi/js";
 import Navbar from "@/Components/Partials/Main/Navbar.vue";
import LayoutMainGuest from "@/Layouts/LayoutMainGuest.vue";




const mainStore = useMainStore();

</script>

<template>
  <LayoutMainGuest>
      <Navbar></Navbar>
  </LayoutMainGuest>
</template>
