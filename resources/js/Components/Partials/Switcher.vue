<script setup>
import {computed, defineEmits,ref} from "vue";

const props = defineProps({
  w: {
    type: String,
    default: "w-6",
  },
  h: {
    type: String,
    default: "h-6",
  },
  size: {
    type: [String, Number],
    default: null,
  },
    name: {
      type: String,
      required: true
    },
    valueA: {
      type: Number,
      required: true
    },
    valueB: {
        type: Number,
        required: true
    },
    labelA: {
        type: String,
        required: true,
        default: 'no'
    },
    labelB: {
        type: String,
        required: true,
        default: 'yes'
    },
    value: {
        required: true
    },
    topLabel:{
        type: String
    }


});
const val = ref(props.value)
const emit = defineEmits(['onChange'])
</script>

<template>
    <div class="relative w-full mb-7 z-10 group">
        <label class="text-sm text-gray-500 dark:text-gray-400 duration-300   scale-75 top-0 z-10 origin-[0]  left-0  0 absolute">{{props.topLabel}}</label>
        <ul class="flex pt-7">
            <li>
                <input v-model="val" type="radio" @change="$emit('onChange',$event.target.value)" :name="props.name" :id="`${props.name}_${props.labelA}_id`" :value="props.valueA" class="hidden peer">
                <label :for="`${props.name}_${props.labelA}_id`" class="inline-flex items-center justify-between w-full px-5 py-1 text-gray-500 bg-white border border-gray-200  cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                    <div class="block" v-text="props.labelA"></div>
                </label>
            </li>
            <li>
                <input type="radio" v-model="val" @change="$emit('onChange',$event.target.value)" :name="props.name" :id="`${props.name}_${props.labelB}_id`" :value="props.valueB" class="hidden peer">
                <label :for="`${props.name}_${props.labelB}_id`" class="inline-flex items-center justify-between w-full px-5 py-1 text-gray-500 bg-white border border-gray-200  cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                    <div class="block" v-text="props.labelB"></div>
                </label>
            </li>
        </ul>
    </div>
    <slot />
</template>
