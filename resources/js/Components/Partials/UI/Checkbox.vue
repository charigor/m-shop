<template>
    <div class="flex flex-wrap my-2 items-center">
        <span>
            <input type="checkbox" v-model="model" :value="value" :class="props.class" :id="`checkbox${label}${value}`" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
            <label :for="`checkbox${label}${value}`" class="w-full py-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300" v-html="label"></label>
       </span>
    </div>
</template>

<script setup >
import { computed, defineEmits } from "vue";
const props = defineProps({
    modelValue: { type: [Array, Boolean] },
    value: { type: [Boolean, Object,Number] },
    label: { type: String },
    class: {
        type: [String,null],
        default: null
    }

});
const emit = defineEmits(["update:modelValue"]);
const model = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit("update:modelValue", value);
    },
});
</script>
