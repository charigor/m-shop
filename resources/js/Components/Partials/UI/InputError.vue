<template>
    <div class="block">
        <p class="mt-2 text-sm text-red-600 dark:text-red-500" v-if="props.target">{{props.target}}</p>
        {{props.target}}
    </div>
</template>

<script setup >
const props = defineProps(['target']);
</script>
