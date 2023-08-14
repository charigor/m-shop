<template>
    <div>
        <template v-if="props.size == 1">
            <h1 class="text-5xl font-extrabold dark:text-white" :class="props.class" v-html="props.content"></h1>
        </template>
        <template v-if="props.size == 2">
            <h2 class="text-4xl font-bold dark:text-white" :class="props.class" v-html="props.content"></h2>
        </template>
        <template v-if="props.size == 3">
            <h3 class="text-3xl font-bold dark:text-white" :class="props.class" v-html="props.content"></h3>
        </template>
        <template v-if="props.size == 4">
            <h4 class="text-2xl font-bold dark:text-white" :class="props.class" v-html="props.content"></h4>
        </template>
        <template v-if="props.size == 5">
            <h5 class="text-xl font-bold dark:text-white" :class="props.class" v-html="props.content"></h5>
        </template>
        <template v-if="props.size == 6">
            <h5 class="text-lg font-bold dark:text-whiteЭ" :class="props.class" v-html="props.content"></h5>
        </template>
    </div>
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
    }
});
</script>

