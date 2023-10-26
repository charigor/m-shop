<template>
      <label class="font-bold  inline-block dark:text-white" :class="props.class" :for="props.for" v-text="props.content"></label>
</template>

<script setup>
const props = defineProps({
    size: {
        type: [String,Number],
        default: 1
    },
    class: {
        type: [String,null],
        default: null
    },
    content: {
        type: String,
        required: true
    },
    for: {
      type: String,
      required: true
    }
});
</script>

