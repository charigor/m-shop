<template>

    <SectionMain>
        <div class="flex items-center justify-between mb-2 row">
            <div class="w-1/4 col-6">
                <input  type="search" @input="search" :value="params.search" aria-label="Search" :placeholder="$t('global.search')+'...'" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </div>
            <slot   name="create"></slot>
        </div>
        <div class="flex mb-2 justify-between row">
            <div class="col-6">
                <TablePaginate @link="(e) => { this.params.page = e}" class="mt-6" :data="data.meta"/>
            </div>
            <div class="w-1/6 mt-5 col-6">
                <select @change="changePages" :value="params.perPage" class="bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                    <option value="5" >5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                </select>
            </div>
        </div>
        <table class="table table-auto w-full bg-white dark:bg-gray-700 shadow rounded">
            <thead>

            <tr class="text-left font-bold">
                <th>
                    <label class="checkbox">
                        <input type="checkbox" v-model="allSelected" @click="selectAll" />
                        <span class="check" />
                    </label>
                </th>
                <template v-for="column in columns">
                    <th class="pb-4 pt-6 px-6" v-if="column.value">
                        <button class="flex items-center" @click="sort(column.label)">
                            {{capitalized(column.trans)}}
                            <template v-if="column.sorting">
                                <BaseIcon  v-if="(params.field === column.label && params.direction === 'asc')" :path="mdiSortAscending" />
                                <BaseIcon v-else-if="(params.field === column.label && params.direction === 'desc')"  :path="mdiSortDescending" />
                            </template>
                        </button>
                    </th>
                </template>
                <th class="pb-4 pt-6 px-6 lg:w-6 whitespace-nowrap justify-start"><p>{{$t('global.action')}}</p></th>
            </tr>
            <tr class="text-left font-bold">
                <td>
                </td>
                <template v-for="column in columns">
                    <td v-if="column.value">
                        <input  v-if="column.type === 'number'" :type="column.type" @input="filter" :value="params.filter[column.label]" :aria-label="column.label" :placeholder="column.trans" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <input  v-else-if="column.type === 'text'" :type="column.type" @input="filter" :value="params.filter[column.label]" :aria-label="column.label" :placeholder="column.trans" class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
                        <template v-else-if="column.type === 'select'">
                            <slot :name="`select_${column.label}`" :filter="filter"></slot>
                        </template>
                        <VueDatePicker class="dp__theme_dark" v-else-if="column.type === 'date'"  range :dark="darkMode" :partial-range="false"   multi-calendars v-model="params.filter[column.label]" :aria-label="column.label" ></VueDatePicker>
                    </td>
                </template>
                <td>
                    <template class="flex items-center">
                        <BaseButtons type="justify-start lg:justify-start" no-wrap>
                            <BaseButton
                                :icon="mdiBackspace"
                                middle
                                color="gray"
                                @click="reset"
                            />
                            <BaseButton
                                :icon="mdiDotsVerticalCircle"
                                middle
                                color="gray"
                                @click.stop="columnShow = !columnShow" data-dropdown-toggle="dropdownDots"
                            />
                            <div v-click-outside="() => {columnShow = false}" v-if="columnShow"  class="absolute top-[50px] right-[2px] z-10 bg-white rounded-lg shadow w-60 dark:bg-gray-700">
                                <ul  class=" px-3 py-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownButton">
                                    <li v-for="item in columns">
                                        <div class="flex p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <label class="relative inline-flex items-center w-full cursor-pointer">
                                                <input type="checkbox" v-model="item.value" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{item.trans}}</span>
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </BaseButtons>
                    </template>
                </td>
            </tr>
            </thead>

            <draggable  @end="sorting" v-model="this.dragItems"  item-key="id"  tag="tbody" handle=".handle"  v-if="this.dragItems.length">
                    <template #item="{element}">
                    <tr  class="hover:bg-gray-100  focus-within:bg-gray-100 ">
                        <td class="group">
                            <div class="flex">
                                <label class="checkbox inline-block m-1 " style="vertical-align: middle">
                                    <input type="checkbox" :value="element" v-model="selected" />
                                    <span class="check mt-1" />
                                </label>
                                <div class="m-1 align-self-center">
                                    <BaseIcon type="mdi" size="30" middle style="vertical-align: middle" class="handle inline-block delay-75 cursor-move hidden group-hover:block" :path=" mdiDotsGrid"></BaseIcon>
                                </div>
                            </div>
                        </td>
                        <template v-for="column in columns" >
                            <td class="border-b-0  before:hidden" :class="{'w-px': (column.label === 'id'),'lg:w-auto': (column.label !== 'id')}" v-if="column.value">
                                <Link class="flex items-center justify-center text-center px-6 py-4 focus:text-indigo-500" :href="link(element)">
                                    <div v-if="column.type === 'media'" class="center" style="width: 3rem">
                                       <img style="width: 3rem" :src="element['media'].length ? element['media'][0]['preview_url']: ''" alt="">
                                    </div>
                                    <div v-else>
                                        {{element[column.label] }}
                                    </div>
                                </Link>
                            </td>
                        </template>
                        <td >
                            <template class="flex items-center">
                                <BaseButtons type="justify-start lg:justify-start" no-wrap>
                                        <BaseLink
                                            color="gray"
                                            middle
                                            :href="`${baseUrl}/${element.id}/edit`"
                                            :icon="mdiCircleEditOutline"
                                            >
                                        </BaseLink>
                                        <BaseButton
                                            color="danger"
                                            :icon="mdiTrashCan"
                                            middle
                                            @click="showDeleteModal(element)"
                                        />
                                </BaseButtons>
                            </template>
                        </td>
                    </tr>
                    </template>

            </draggable>
            <tbody v-if="!this.dragItems.length">
                <tr >
                    <td class="px-6 py-4 border-t" colspan="1000">{{$t('global.no_items_found')}}</td>
                </tr>
            </tbody>



        </table>
        <div class="flex  justify-between row">
            <div class="col-6">
             <TablePaginate @link="(e) => { this.params.page = e}" class="mt-6" :data="data.meta"/>
            </div>
            <div class="w-1/6 mt-5 col-6">
                <select @change="changePages" :value="params.perPage" class="bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50" placeholder="Per Page">
                    <option value="5" >5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                </select>
            </div>
        </div>

        <CardBoxModal
            v-model="isModalDangerActive"
            :title="$t('global.please_confirm')"
            button="danger"
            has-cancel
            @confirm="confirmDelete"
        >
            Are you sure you want to delete these {{this.tableName || 'Elements'}}?
            <ul><li v-for="item in selected"><b>{{item.id}}.{{item[this.deleteTitle]}}</b></li></ul>
        </CardBoxModal>
    </SectionMain>



</template>

<script>
import { useMainStore } from "@/stores/main.js";
import { darkModeKey, styleKey } from "@/config.js";
import {Link, usePage,router} from '@inertiajs/vue3';
    import SectionMain from "@/Components/Partials/SectionMain.vue";
    import SectionTitleLineWithButton from "@/Components/Partials/SectionTitleLineWithButton.vue";
    import BaseButtons from "@/Components/Partials/BaseButtons.vue"
    import BaseButton from "@/Components/Partials/BaseButton.vue"
    import BaseLink from "@/Components/Partials/BaseLink.vue"
    import BaseIcon from "@/Components/Partials/BaseIcon.vue"
    import TablePaginate from "@/Components/Partials/TablePaginate.vue"
    import CardBoxModal from '@/Components/Partials/CardBoxModal.vue'
    import pickBy from 'lodash/pickBy'
    import debounce from 'lodash.debounce'
    import TableCheckboxCell from '@/Components/Partials/TableCheckboxCell.vue'
    import VueDatePicker from '@vuepic/vue-datepicker'
    import '@vuepic/vue-datepicker/dist/main.css'
    import throttle from 'lodash/throttle'
    import draggable from 'vuedraggable'
    import {
    mdiEye,
    mdiTrashCan,
    mdiChevronUp,
    mdiChevronDown,
    mdiFilter,
    mdiBackspace,
    mdiDotsVerticalCircle,
    mdiSortDescending,
    mdiSortAscending,
    mdiMagnify,
    mdiAccountBoxMultipleOutline,
    mdiCircleEditOutline,
    mdiMenu,
    mdiDotsGrid

} from "@mdi/js";
    import {useStyleStore} from "@/stores/style";
    import {mapState} from "pinia";
import {wTrans} from "laravel-vue-i18n";
export default {
    name: "DataTableDrag",
    components: {
        BaseLink,
        Link,
        SectionMain,
        TablePaginate,
        BaseButtons,
        BaseButton,
        BaseIcon,
        VueDatePicker,
        CardBoxModal,
        SectionTitleLineWithButton,
        TableCheckboxCell,
        draggable

    },
    props: {
        data: {
            type: Object,
            required: true
        },
        search: {
            type: [String,null],
            required: true
        },
        filter: {
            type: [Object,null],
            required: true
        },
        urlPrefix: {
            type:  String,
            required: true
        },
        baseUrl: {
            type:  String,
            required: true
        },
        deleteTitle: {
            type:  String,
            required: true
        },
        tableName: {
            type:  String,
        },
        columns: {
          type:  Object,
          required: true
        },
    },
    data() {
        return {
            params: {
                search: this.search,
                perPage: this.data.meta.per_page,
                field: null,
                direction: null,
                page: null,
                filter: {}
            },
            isModalDangerActive: false,
            columnShow: false,
            selected: [],
            allSelected: false,
            mdiEye,
            mdiTrashCan,
            mdiChevronUp,
            mdiChevronDown,
            mdiFilter,
            mdiBackspace,
            mdiDotsVerticalCircle,
            mdiSortDescending,
            mdiSortAscending,
            mdiMagnify,
            mdiAccountBoxMultipleOutline,
            mdiCircleEditOutline,
            mdiDotsGrid,
            mdiMenu,
            dragging: true,
            dragItems: this.data.data

        }
    },
    computed:{
        ...mapState(useStyleStore, ['darkMode'])

    },
    watch: {
        params: {
            deep: true,
            handler: debounce(function(el) {

                this.selected = []
                this.allSelected = false
                let params = pickBy(this.params)
                this.$inertia.get(this.urlPrefix, params, {preserveState: true,  preserveScroll: true})
            }, 500)
        }

    },

    methods: {
        link(param) {
            let  link = `${this.baseUrl}/${param.id}/edit`;
            if(param.hasOwnProperty('children')){
                if(param.children.length ){
                   return   `${this.baseUrl}/${param.id}`;
                }
                link =  `${this.baseUrl}/${param.id}/edit`;
            }
            return link;
        },
        selectAll() {
            this.selected = []
            if (!this.allSelected) {
                for (let item in this.data.data) {
                    if(this.checkItem(item)){
                        this.selected.push(this.data.data[item]);
                    }
                }
            }
        },
        search(event){
            this.params.page = null
            this.params.search = event.target.value
        },
        reset() {
            this.params.filter.created_at = null
            this.resetSelects(this.filter)
            for (let paramsKey in this.params) {
                if(typeof this.params[paramsKey] === 'object') {
                    for (let inner in this.params[paramsKey]) {
                        delete(this.params[paramsKey][inner])
                    }
                }else{
                    paramsKey !== 'perPage' ? this.params[paramsKey] = null : ''
                }

            }

        },
        resetSelects(filter){
            for(let item in filter){
                let element =  document.querySelector(`select[aria-label=${item}]`);
                if(element) element.selectedIndex = null
            }
        },
        filter(event){
            this.params.page = null
            if(event.target.value == ''){
                delete(this.params.filter[event.target.getAttribute('aria-label').toLowerCase()])
            }else{
                this.params.filter[event.target.getAttribute('aria-label').toLowerCase()] = event.target.value
            }
        },
        sort(field){
            this.params.field = field
            this.params.direction = this.params.direction === 'asc' ? 'desc' : 'asc'
        },
        sorting(){
            router.post(`${this.baseUrl}/sort`, {el:this.dragItems, id:  window.location.href.split('/').pop()})
        },
        capitalized(name = ' ') {
            const capitalizedFirst = name[0].toUpperCase();
            const rest = name.slice(1);

            return capitalizedFirst + rest;
        },
        showDeleteModal(el){
            this.isModalDangerActive = true;
            if(el && this.checkItem(el)){
                this.selected.push(el)
            }
        },
        checkItem(item){
            let pointer = true;
            this.selected.forEach((e) => {
                if(e.id === item.id){
                    pointer = false;
                }
            })
            return pointer;
        },
        confirmDelete()
        {
            const ids = this.selected.map(i => i['id']);
            this.$inertia.post(`${this.urlPrefix}/delete`, {ids:ids})
            this.page = 1;
            this.selected = []
        },
        changePages(e){
            this.params.page = 1;
            this.params.perPage = e.target.value
        }
    },
    updated(){
        this.dragItems = this.data.data
    }
}
</script>
<style>
.dark .dp__theme_dark {
--dp-background-color: rgb(55 65 81 / var(--tw-bg-opacity));
--dp-text-color: rgb(229 231 235 / var(--tw-text-opacity));
--dp-hover-color: #484848;
--dp-hover-text-color: #ffffff;
--dp-hover-icon-color: #959595;
--dp-primary-color: #005cb2;
--dp-primary-text-color: #ffffff;
--dp-secondary-color: #a9a9a9;
--dp-border-color: rgb(75 85 99 / var(--tw-border-opacity));
--dp-menu-border-color: #2d2d2d;
--dp-border-color-hover: #aaaeb7;
--dp-disabled-color: #737373;
--dp-scroll-bar-background: #212121;
--dp-scroll-bar-color: #484848;
--dp-success-color: #00701a;
--dp-success-color-disabled: #428f59;
--dp-icon-color: #959595;
--dp-danger-color: #e53935;
--dp-highlight-color: red;
}
</style>
