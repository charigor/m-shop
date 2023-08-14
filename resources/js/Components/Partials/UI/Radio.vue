<template>
    <component :is="'div'">
        <span class="flex flex-wrap my-2 items-center">
            <input type="radio" :checked="isChecked" :value="value" :class="props.class" @change="$emit('update:modelValue', $event.target.value)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"> {{props.label}}</label>
        </span>
    </component>
</template>

<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    checked: {
        type: [Boolean],
        required: true,
    },
    modelValue: {
        default: ""
    },
    label: {
        type: String,
        default: "",
        required:true
    },
    value: {
        type: String,
        default: undefined
    },
    class: {
        type: [String,null],
        default: null
    }

});

const isChecked = computed(() => {
    return props.modelValue == props.value
});
</script>
