<script >
import { useMainStore } from "@/stores/main";
import {
    mdiAccountMultiple,
    mdiCartOutline,
    mdiChartTimelineVariant,
    mdiMonitorCellphone,
    mdiReload,
    mdiGithub,
    mdiChartPie,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import { computed, ref, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
 import CardBox from "@/Components/Partials/CardBox.vue";
import SectionMain from "@/Components/Partials/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/Partials/SectionTitleLineWithButton.vue"
import MyInput from "@/Components/Partials/MyInput.vue";
import Table from "@/Components/Partials/Table.vue"
export default {
    components: {
        SectionMain,
        SectionTitleLineWithButton,
        Table,
        MyInput
    },
    layout: LayoutAuthenticated,
    props: ['test'],
    data: () => ({
        title: 'Some title',
        form: {
            name: ''
        }
    }),
    methods: {
        addUser(){
            this.test.shift({'id' : '1','name': 'some name','email': 'sddf','created_at': 'ddd'})
        }
    }
}
</script>
<template>
    <LayoutAuthenticated>
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiChartTimelineVariant"
                title="Test"
                main
            >
            </SectionTitleLineWithButton>
            <MyInput v-model="form.name" ></MyInput>
            {{form.name}}
            <button @click="addUser">add</button>
            <TransitionGroup name="table-list" tag="ul">
                <li v-for="item in test" :key="item.id">
                    {{ item.name }}
                </li>
            </TransitionGroup>

            <Table @modify="(param) => {this.title = param}" :data="test">

                <template #create >
                <button @click="test = [{'id' : '1','name': 'some name','email': 'sddf','created_at': 'ddd'}]">Create</button>
                </template>
            </Table>
        </SectionMain>

    </LayoutAuthenticated>
</template>
<style scoped>
.table-list-enter-active,
.table-list-leave-active {
    transition: all 0.5s ease;
}
.table-list-enter-from,
.table-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>

