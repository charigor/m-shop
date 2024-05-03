
import { createApp } from 'vue/dist/vue.esm-bundler';

import axios from 'axios';

import checkoutComponent from "@/Front/Components/CheckoutComponent.vue";

import "vue-select/dist/vue-select.css";

import VClickOutside from "@/VClickOutside.js";
window.axios = axios;

const app = createApp({})
app.component('checkout-component', checkoutComponent)

app.directive('click-outside',VClickOutside)
app.mount('#app');

