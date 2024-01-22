
import { createApp } from 'vue/dist/vue.esm-bundler';
import axios from 'axios';
import firstStep from "@/Front/Components/FirstStep.vue";
window.axios = axios;



const app = createApp({})

app.component('first-step', firstStep)
app.mount('#app')
