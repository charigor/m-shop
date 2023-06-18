<script setup>
import VueTailwindDatepicker from 'vue-tailwind-datepicker'
import BaseIcon from "@/Components/Partials/BaseIcon.vue"
import {
    mdiCalendarMonthOutline

} from "@mdi/js";
import { ref, watch,defineEmits,computed } from 'vue';
const dateValue = ref('');


const emit = defineEmits(['goDate'])
const props = defineProps(['label'])
const formatter = ref({
    date: 'DD-MM-YYYY',
    month: 'MMM'
})
watch(dateValue, (newDate) =>  {
    let dateArray = '';
    if(newDate !== ''){
        dateArray = newDate.split('~');
    }

   emit('goDate',dateArray,props.label)
},{deep: true})
function setValue() {
    console.log('ntn')
   dateValue.value = '';
}

</script>

<template>
    <div>

    <vue-tailwind-datepicker  v-slot="{ value, placeholder, clear }" v-model="dateValue">
<!--        <input type="text" /> <BaseIcon  :path="mdiCalendarMonthOutline" />-->
    </vue-tailwind-datepicker>
    </div>
</template>
<!--<style>-->
<!-- .dark .vtd-datepicker, .dark .vtd-datepicker button{-->
<!--     background-color: rgb(55 65 81 / var(&#45;&#45;tw-bg-opacity));-->
<!--     box-shadow: var(&#45;&#45;tw-ring-offset-shadow, 0 0 #0000), var(&#45;&#45;tw-ring-shadow, 0 0 #0000), var(&#45;&#45;tw-shadow);-->
<!-- }-->
<!--</style>-->
