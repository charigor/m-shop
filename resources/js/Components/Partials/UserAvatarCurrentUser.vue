<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import UserAvatar from "@/Components/Partials/UserAvatar.vue";
const userName = computed(() => usePage().props.auth.user.name);
</script>

<template>
    <UserAvatar :username="userName" api="initials">
        <slot />
    </UserAvatar>
</template>
