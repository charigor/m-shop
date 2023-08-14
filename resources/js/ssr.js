import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import {createInertiaApp, Link,router} from '@inertiajs/vue3';
import { useStyleStore } from "./stores/style.js";
import { darkModeKey, styleKey } from "./config.js";
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import {i18nVue} from "laravel-vue-i18n";
import VClickOutside from "@/VClickOutside";
import {createPinia} from "pinia";

const appName = 'Laravel';
const t = 'Igor';
const pinia = createPinia();
createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        t: `${t}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ App, props, plugin }) {
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(Link,router)
                .use(i18nVue)
                .directive('click-outside',VClickOutside);
        }
    })
);



