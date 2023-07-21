<script setup>
import { computed, ref, onMounted,reactive } from "vue";
import { Head,router } from "@inertiajs/vue3";
 import { useMainStore } from "@/stores/main";
import VueDropzone from '@/Components/Partials/VueDropzone/DropzoneDraggable.vue'
import {
  mdiAccountMultiple,
  mdiCartOutline,
  mdiChartTimelineVariant,
  mdiMonitorCellphone,
  mdiReload,
  mdiGithub,
  mdiChartPie,
} from "@mdi/js";
 import * as chartConfig from "@/Components/Partials/Charts/chart.config.js";
 import  LineChart from "@/Components/Partials/Charts/LineChart.vue";
  import SectionMain from "@/Components/Partials/SectionMain.vue";
  import CardBoxWidget from "@/Components/Partials/CardBoxWidget.vue";
  import CardBox from "@/Components/Partials/CardBox.vue";
 import TableSampleClients from "@/Components/Partials/TableSampleClients.vue";
 import NotificationBar from "@/Components/Partials/NotificationSucces.vue";
 import BaseButton from "@/Components/Partials/BaseButton.vue";
 import CardBoxTransaction from "@/Components/Partials/CardBoxTransaction.vue";
 import CardBoxClient from "@/Components/Partials/CardBoxClient.vue";
  import LayoutGuest from "@/Layouts/LayoutGuest.vue";
  import SectionTitleLineWithButton from "@/Components/Partials/SectionTitleLineWithButton.vue";
 import SectionBannerStarOnGitHub from "@/Components/Partials/SectionBannerStarOnGitHub.vue";

const chartData = ref(null);

const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData();
};

onMounted(() => {
  fillChartData();
});



const mainStore = useMainStore();

const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);

const search_trigger = ((el) => {
    router.get('/',{'search': el.target.value},{preserveState: true,  preserveScroll: true});
})
const props = defineProps({
    data: {}
})
const form = reactive({
    image : []
})
const results = ref(props.data)

</script>

<template>
  <LayoutGuest>
      <Head title="Main" />

      <div>
          <form class="flex items-center" >
              <label for="simple-search" class="sr-only">Search</label>
              <div class="relative w-full">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                      <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"/>
                      </svg>
                  </div>
                  <input type="text" id="simple-search" @input="search_trigger" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search branch name..." required>
              </div>
          </form>
          <div class="m-4" v-if="props.data">
              <div>
                  <template v-for="data in props.data">
                      <ul>
                          <li  v-for="item in data.category.translation">
                              <div class="border-b my-2" v-if="item.locale === useMainStore().lang">
                                  <span>Title: {{item.title}}</span>
                                  <p class="mb-2"> Text: {{item.description}}</p>
                              </div>

                          </li>
                      </ul>
                  </template>

              </div>
              <ul>
                  <li v-for="item in props.data">
                      <div class="border-b my-2">
                          <span>Title: {{item.name}}</span>
                      </div>
                      <p class="mb-2"> Text: {{item.description}}</p>
                  </li>
              </ul>
          </div>
      </div>
      <VueDropzone :w="Number(150)" :h="Number(150)" multiple="1"  viewType="square" @removeImage="(file) => form.image = form.image.filter((item) => item !== file)" @loadImages="(file) => form.image.push(file)" path="/storeMedia" :files="[]" :csrf="$page.props.csrf_token"></VueDropzone>
  </LayoutGuest>
</template>
