<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ref } from 'vue';

export default {
    data: () => ({
        open: ['Dashboard'],
        admins: [
            ['Management', 'mdi-account-multiple-outline'],
            ['Settings', 'mdi-cog-outline'],
        ],
        cruds: [
            ['Create', 'mdi-plus-outline'],
            ['Read', 'mdi-file-outline'],
            ['Update', 'mdi-update'],
            ['Delete', 'mdi-delete'],
        ],
        drawer: true,
        items: [
            { title: 'Manager', icon: 'mdi-account' ,link: 'user.index'
                // ,sub : {
                //     title: 'Permissions' , icon:'mdi-security'
                // }
            },
        ],
        rail: true,
        // open: true
    }),
}
</script>

<template>
    <Head title="Admin" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>
<!--            <div>-->
<!--                <v-app-bar app elevation="2" fixed clipped-left>-->
<!--                    <v-app-bar-nav-icon ></v-app-bar-nav-icon>-->

<!--                    <v-toolbar-title>Magic Shop</v-toolbar-title>-->

<!--                    <v-spacer></v-spacer>-->

<!--                    <v-menu offset-y origin="center center" :nudge-bottom="10" transition="scale-transition">-->
<!--                        <template v-slot:activator="{ on, attrs }">-->
<!--                            <v-btn icon large text-->
<!--                                   v-bind="attrs"-->
<!--                                   v-on="on">-->
<!--                            </v-btn>-->
<!--                        </template>-->
<!--                    </v-menu>-->
<!--                </v-app-bar>-->
<!--            </div>-->

<!--                <v-card>-->

<!--                    <v-layout>-->

<!--                        <v-navigation-drawer-->
<!--                            class="mx-auto"-->
<!--                            width="800"-->
<!--                            v-model="drawer"-->
<!--                            :rail="rail"-->
<!--                            permanent-->
<!--                            @click="rail = false"-->
<!--                        >-->
<!--                            <v-list  v-model:opened="open">-->

<!--                            <v-list-item-->
<!--                                prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"-->
<!--                                title="John Leider"-->
<!--                                nav-->
<!--                            >-->
<!--                                <template v-slot:append>-->
<!--                                    <v-btn-->
<!--                                        variant="text"-->
<!--                                        icon="mdi-chevron-left"-->
<!--                                        @click.stop="rail = !rail"-->
<!--                                    ></v-btn>-->
<!--                                </template>-->
<!--                            </v-list-item>-->
<!--                                <v-list-group value="Management" v-for="(item, i) in items">-->
<!--                                    <template v-slot:activator="{ props }">-->
<!--                                        <v-list-item-->
<!--                                            v-bind="props"-->
<!--                                            :key="i"-->
<!--                                            :prepend-icon="item.icon"-->
<!--                                            :value="item.title"-->
<!--                                            :title="item.title"-->
<!--                                        ></v-list-item>-->
<!--                                    </template>-->

<!--                                    <v-list-item-->
<!--                                        v-bind="props"-->
<!--                                    >-->
<!--                                        <Link :href="route(item.link)" :active="route().current(item.link)" >{{item.title}}</Link>-->
<!--                                    </v-list-item>-->
<!--                                </v-list-group>-->
<!--                            </v-list>-->

<!--                        </v-navigation-drawer>-->
<!--                        <v-main style="height: 250px"></v-main>-->
<!--                    </v-layout>-->
<!--                </v-card>-->



<!--        <div class="py-12">-->
<!--            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">-->
<!--                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">-->
<!--                    <div class="p-6 text-gray-900 dark:text-gray-100">You're logged in!</div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

    </AuthenticatedLayout>

</template>

