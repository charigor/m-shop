<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ref } from 'vue';

export default {
    data: () => ({
        open: ['Management'],
        admins: [
            ['Management', 'mdi-account-multiple-outline'],
            ['settings', 'mdi-cog-outline'],
        ],
        cruds: [
            ['Create', 'mdi-plus-outline'],
            ['Read', 'mdi-file-outline'],
            ['Update', 'mdi-update'],
            ['Delete', 'mdi-delete'],
        ],
        drawer: true,
        items: [
            { title: 'Management', icon: 'mdi-account',link: null,sub : { title: 'Users' , icon:'mdi-security',link: 'user.index'}},
        ],
        rail: true,
        // open: true
    }),
}
</script>
<template>

        <v-navigation-drawer
            class="mx-auto"
            width="250"
            v-model="drawer"
            :rail="rail"
            permanent
            @click="rail = false"
        >
            <v-list>
                <v-list-item
                    prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                    title="John Leider"
                    nav
                >
                    <template v-slot:append>
                        <v-btn
                            variant="text"
                            icon="mdi-chevron-left"
                            @click.stop="rail = !rail"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-list-group :value="item.title" v-for="(item, i) in items">
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :key="i"
                            :prepend-icon="item.icon"
                            :value="item.title"
                            :title="item.title"
                        ></v-list-item>
                    </template>

                    <v-list-item v-if="item.sub"
                                 v-bind="props"
                    >
                        <Link :href="route(item.sub.link)" :active="route().current(item.sub.link)" >{{item.sub.title}}</Link>
                    </v-list-item>
                </v-list-group>
            </v-list>

        </v-navigation-drawer>

</template>
