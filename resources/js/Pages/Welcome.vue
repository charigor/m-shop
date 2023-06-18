<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ref } from 'vue';
// const showingNavigationDropdown = ref(false);
// export default {
//     data: () => ({
//         open: ['Dashboard'],
//         drawer: true,
//         items: [
//             { title: 'Management', icon: 'mdi-account',link: null,sub : [{ title: 'Users' , icon:'mdi-security',link: 'user.index'}]},
//         ],
//         rail: true,
// // open: true
//     }),
// }
// defineProps({
//     canLogin: {
//         type: Boolean,
//     },
//     canRegister: {
//         type: Boolean,
//     },
//     laravelVersion: {
//         type: String,
//         required: true,
//     },
//     phpVersion: {
//         type: String,
//         required: true,
//     },
// });
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Welcome</h2>
        </template>

<!--        <v-card>-->

<!--            <v-layout>-->

<!--                <v-navigation-drawer-->
<!--                    class="mx-auto"-->
<!--                    width="800"-->
<!--                    v-model="drawer"-->
<!--                    :rail="rail"-->
<!--                    permanent-->
<!--                    @click="rail = false"-->
<!--                >-->
<!--                    <v-list  v-model:opened="open">-->

<!--                        <v-list-item-->
<!--                            prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"-->
<!--                            title="John Leider"-->
<!--                            nav-->
<!--                        >-->
<!--                            <template v-slot:append>-->
<!--                                <v-btn-->
<!--                                    variant="text"-->
<!--                                    icon="mdi-chevron-left"-->
<!--                                    @click.stop="rail = !rail"-->
<!--                                ></v-btn>-->
<!--                            </template>-->
<!--                        </v-list-item>-->
<!--                        <v-list-group :value="item.title" v-for="(item, i) in items">-->
<!--                            <template v-slot:activator="{ props }">-->
<!--                                <v-list-item-->
<!--                                    v-bind="props"-->
<!--                                    :key="i"-->
<!--                                    :prepend-icon="item.icon"-->
<!--                                    :value="item.title"-->
<!--                                    :title="item.title"-->
<!--                                ></v-list-item>-->
<!--                            </template>-->

<!--                            <v-list-item v-if="item.sub" v-for="(sub,i) in item" :key="i"-->
<!--                                         v-bind="props"-->
<!--                            >-->
<!--                                <Link :href="route(sub.link)" :active="route().current(sub.link)" >{{sub.title}}</Link>-->
<!--                            </v-list-item>-->
<!--                        </v-list-group>-->
<!--                    </v-list>-->

<!--                </v-navigation-drawer>-->
<!--                <v-main style="height: 250px"></v-main>-->
<!--            </v-layout>-->
<!--        </v-card>-->

    </AuthenticatedLayout>

</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
