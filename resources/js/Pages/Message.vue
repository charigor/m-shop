
<script>
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import { computed, ref, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";

import SectionMain from "@/Components/Partials/SectionMain.vue";

import BaseButtons from "@/Components/Partials/BaseButtons.vue"
import BaseButton from "@/Components/Partials/BaseButton.vue"
import BaseIcon from "@/Components/Partials/BaseIcon.vue"
import axios from "axios";

import {
    mdiEye,
    mdiTrashCan,
    mdiChevronUp,
    mdiChevronDown,
    mdiFilter,
    mdiBackspace,
    mdiSend

} from "@mdi/js";


export default {
        components: {
            SectionMain,
            BaseButtons,
            BaseButton,
            BaseIcon,
            Head,
            Link,


        },
        layout: LayoutAuthenticated,
            props: {
            messages: Object,
        },
        data() {
            return {
                timer : null,
                notifications: null,
                mdiEye,
                mdiTrashCan,
                mdiChevronUp,
                mdiChevronDown,
                mdiFilter,
                mdiBackspace,
                mdiSend,
                form: {
                    body: null
                }
            }
        },

        methods: {
            async send() {
                try {
                    let response = await axios.post('/admin/messages', {body: this.form.body});
                    this.messages.unshift(response.data)
                } catch (e) {
                    console.log(e)
                }

            },
            async getMessage() {
                let response = await axios.post('/admin/messages/notifications', {});
                if(response){
                    this.notifications = response.data.messages
                }
            },
            async markAsRead(id){
                let response = await axios.post('/admin/messages/mark_read', {'id': id});
                if(response){
                    this.notifications = response.data.messages
                }
            }
        },
        created() {
            window.Echo.channel('store_message').listen('.store_message',res => {
                this.messages.unshift(res.message)
            });
        },
        mounted() {
            this.getMessage()
            this.timer = setInterval(() => {
                this.getMessage()
            }, 10000)
        },
        beforeDestroy() {
            clearInterval(this.timer)
        }
}
</script>

<template>

        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiChartTimelineVariant"
                title="Messages"
                main
            >
            </SectionTitleLineWithButton>
            <div class="flex flex-col w-1/6">
                <ul>
                    <li class="my-2" v-for="item in this.notifications">
                        {{item.data.name}} : {{item.data.message}}  <button class="inline-block border-2 rounded-8 p-2 bg-blue-800" type="button" @click.prevent="markAsRead(item.id)">Mark as Read</button>
                    </li>
                </ul>
                <textarea name="" id="" cols="30" v-model="form.body" rows="5"></textarea>

                    <BaseButton
                        :icon="mdiSend"
                        middle
                        @click.prevent="send"
                    ></BaseButton>

            </div>
            <ul>
                <li class="p-3 my-2 bg-gray-300 rounded" v-for="item in messages" :key="item.id">
                    <p class="p-2 bg-gray-100 rounded">{{item.body}}</p>
                    <div class="block my-1 text-right">{{item.created_at}}</div>
                </li>
            </ul>
        </SectionMain>
</template>










