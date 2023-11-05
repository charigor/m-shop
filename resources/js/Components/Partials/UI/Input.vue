<template>
    <div class="flex flex-wrap my-2 items-center" :class="{'opacity-25' : disabled}">
       <input :disabled="disabled" @input="target = null" type="text" v-model="model" :class="props.class" :id="`input${label}`" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"/>
       <label :for="`input${label}`" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6" v-html="label"></label>
        <InputError :target="target"/>
    </div>
</template>

<script setup >
import { computed, defineEmits } from "vue";
import InputError from "@/Components/Partials/UI/InputError.vue";
const props = defineProps({
    target: {},
    modelValue: { type: [String] },
    label: { type: String },
    disabled: {type: [Boolean]},
    class: {
        type: [String,null],
        default: null
    }

});
const emit = defineEmits(["update:modelValue","update:bindInput"]);
const model = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit("update:modelValue", value);
        emit("update:bindInput", value);
    },
});
</script>
