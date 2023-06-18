
<!--<template >-->

<!--    <v-flex>-->
<!--        <template fluid>-->
<!--            <v-card>-->
<!--                <v-card-title>-->
<!--                    <v-text-field-->
<!--                        v-model="search"-->
<!--                        append-icon="mdi-magnify"-->
<!--                        label="Search"-->
<!--                        single-line-->
<!--                        hide-details-->
<!--                    ></v-text-field>-->
<!--                </v-card-title>-->
<!--                <v-data-table-->
<!--                    :headers="selectedHeaders"-->
<!--                    :items="users"-->
<!--                    :search="search"-->
<!--                    :options.sync="options"-->
<!--                    :server-items-length="totalUsers"-->
<!--                    :loading="loading"-->
<!--                    show-select-->
<!--                    v-model="selected"-->
<!--                    :calculate-widths="calculateWidths"-->
<!--                >-->
<!--                    <template v-slot:top>-->
<!--                        <v-toolbar-->
<!--                            flat-->
<!--                        >-->
<!--                            <v-toolbar-title>{{ title }}</v-toolbar-title>-->
<!--                            <v-divider-->
<!--                                class="mx-4"-->
<!--                                inset-->
<!--                                vertical-->
<!--                            ></v-divider>-->
<!--                            <v-spacer></v-spacer>-->
<!--                            <v-btn :to='{name:"UsersCreate"}'>{{ formTitle }}</v-btn>-->
<!--                            <v-dialog v-model="dialogDelete" persistent max-width="500px">-->
<!--                                <v-card>-->
<!--                                    <v-card-title color="white" class="justify-center">Are you sure you want to delete-->
<!--                                        this item?-->
<!--                                    </v-card-title>-->
<!--                                    <v-divider class="mb-1"></v-divider>-->
<!--                                    <v-card-actions class="justify-center">-->
<!--                                        <div>-->
<!--                                            <v-spacer></v-spacer>-->

<!--                                            <v-list-->
<!--                                                dense-->
<!--                                                nav-->
<!--                                            >-->
<!--                                                <v-list-item-->
<!--                                                    v-for="item in selected"-->
<!--                                                    :key="item.id"-->
<!--                                                >-->
<!--                                                    <v-list-item-content>-->
<!--                                                        <v-list-item-title large>{{ item.id }} <span>{{ item.email }}</span></v-list-item-title>-->
<!--                                                    </v-list-item-content>-->
<!--                                                </v-list-item>-->
<!--                                            </v-list>-->
<!--                                            <v-spacer></v-spacer>-->

<!--                                            <div class="d-flex justify-center">-->
<!--                                                <v-btn outlined class="ma-1" color="secondary" text @click="closeDelete">-->
<!--                                                    Cancel-->
<!--                                                </v-btn>-->
<!--                                                <v-btn outlined class="ma-1" color="error" text-->
<!--                                                       @click="deleteItemConfirm(selected.length <= 1)">OK-->
<!--                                                </v-btn>-->
<!--                                            </div>-->
<!--                                            <v-spacer></v-spacer>-->
<!--                                        </div>-->
<!--                                    </v-card-actions>-->
<!--                                </v-card>-->
<!--                            </v-dialog>-->
<!--                        </v-toolbar>-->
<!--                    </template>-->
<!--                    <template v-slot:body.prepend>-->
<!--                        <tr>-->
<!--                            <td>-->
<!--                                <v-icon v-if="selected.length"-->
<!--                                        @click="deleteItem()"-->
<!--                                >-->
<!--                                    mdi-delete-restore-->
<!--                                </v-icon>-->
<!--                            </td>-->

<!--                            <td v-for="item in selectedHeaders" v-if="item.value != 'actions'">-->

<!--                                <v-text-field v-if="(item.type =='text'  || item.type =='number')"-->
<!--                                    v-model="filters[item.value]"-->
<!--                                    :type="item.type"-->
<!--                                    :label="item.text"-->
<!--                                ></v-text-field>-->
<!--                                <FilterDatepicker ref="tableDatepicker" @filter-event="filterDatepicker" v-model="filters[item.value]"  v-else-if="item.type =='datetime'"-->

<!--                                label="Created at"-->
<!--                                />-->
<!--                                <v-select v-else-->
<!--                                    :items="roles"-->
<!--                                    v-model="filters[item.value]"-->
<!--                                    item-value="id"-->
<!--                                    item-text="name"-->
<!--                                    label="Role"-->
<!--                                >-->
<!--                                </v-select>-->
<!--                            </td>-->
<!--                            <td>-->
<!--                                <div class="d-flex justify-end">-->
<!--                                    <v-btn-->
<!--                                        color="secondary"-->
<!--                                        small-->
<!--                                        class="float-end mr-1"-->
<!--                                        @click="filter">-->
<!--                                        <v-icon aria-hidden="false" small>-->
<!--                                            mdi-magnify-->
<!--                                        </v-icon>-->
<!--                                    </v-btn>-->
<!--                                    <v-btn-->
<!--                                        color="secondary"-->
<!--                                        class="mr-1"-->
<!--                                        @click="reset"-->
<!--                                        small-->
<!--                                    >-->
<!--                                        <v-icon aria-hidden="false" small>-->
<!--                                            mdi-close-->
<!--                                        </v-icon>-->
<!--                                    </v-btn>-->
<!--                                    <SelectDialog :fields="fields" :headers="headers" store_name="users_select"-->
<!--                                                  @apply="selectedHeaders = $event"></SelectDialog>-->
<!--                                </div>-->

<!--                            </td>-->
<!--                        </tr>-->
<!--                    </template>-->
<!--                    <template v-slot:item.actions="{ item }">-->
<!--                        <v-btn icon class="no-bg-hover" :to='{name:"UsersEdit",params:{id:item.id}}'>-->
<!--                            <v-icon small> mdi-pencil</v-icon>-->
<!--                        </v-btn>-->
<!--                        <v-icon-->
<!--                            small-->
<!--                            @click="deleteItem(item)"-->
<!--                        >-->
<!--                            mdi-delete-->
<!--                        </v-icon>-->
<!--                    </template>-->
<!--                </v-data-table>-->
<!--            </v-card>-->
<!--        </template>-->
<!--    </v-flex>-->
<!--</template>-->
<!--<script>-->

<!--export default {-->

<!--    data: () => ({-->
<!--        apiUrl: '/api/admin/users',-->
<!--        calculateWidths: true,-->
<!--        drawer: null,-->
<!--        dialogDelete: false,-->
<!--        title: 'Users',-->
<!--        formTitle: 'Create User',-->
<!--        selected: [],-->
<!--        selectedIndexes: [],-->
<!--        search: '',-->
<!--        totalUsers: 0,-->
<!--        loading: true,-->
<!--        options: {},-->
<!--        users: [],//users data-->
<!--        selectedHeaders: [],//selected items of datatable checkboxes (first column)-->
<!--        roles: [],-->
<!--        // Filter models.-->
<!--        filters: {-->
<!--            id: '',-->
<!--            name: '',-->
<!--            email: '',-->
<!--            role: '',-->
<!--            created_at: ''-->
<!--        },-->

<!--        fields: [-->
<!--            {-->
<!--                text: 'ID',-->
<!--                align: 'start',-->
<!--                value: 'id',-->
<!--                filterable: true,-->
<!--                type: 'number'-->
<!--            },-->
<!--            {-->
<!--                text: 'Name',-->
<!--                align: 'start',-->
<!--                value: 'name',-->
<!--                filterable: true,-->
<!--                type: 'text',-->
<!--            },-->
<!--            {-->
<!--                text: 'Email',-->
<!--                value: 'email',-->
<!--                align: 'start',-->
<!--                filterable: true,-->
<!--                type: 'text',-->
<!--            },-->
<!--            {-->
<!--                text: 'Role',-->
<!--                value: 'roles',-->
<!--                align: 'start',-->
<!--                filterable: true,-->
<!--                type: 'select',-->
<!--            },-->
<!--            {-->
<!--                text: 'Created at',-->
<!--                value: 'created_at',-->
<!--                align: 'start',-->
<!--                filterable: true,-->
<!--                type: 'datetime',-->
<!--            },-->
<!--            {text: 'Actions', align: 'end', value: 'actions', sortable: false, width: "100px",}-->
<!--        ]-->
<!--    }),-->

<!--    computed: {-->
<!--        headers() {-->
<!--            return localStorage.getItem('users_select') ? JSON.parse(localStorage.getItem('users_select')) : [-->
<!--                {-->
<!--                    text: 'ID',-->
<!--                    align: 'start',-->
<!--                    value: 'id',-->
<!--                    filterable: true,-->
<!--                    type: 'number'-->
<!--                },-->
<!--                {-->
<!--                    text: 'Name',-->
<!--                    align: 'start',-->
<!--                    value: 'name',-->
<!--                    filterable: true,-->
<!--                    type: 'text',-->
<!--                },-->
<!--                {-->
<!--                    text: 'Email',-->
<!--                    value: 'email',-->
<!--                    align: 'start',-->
<!--                    filterable: true,-->
<!--                    type: 'text',-->
<!--                },-->
<!--                {-->
<!--                    text: 'Role',-->
<!--                    value: 'roles',-->
<!--                    align: 'start',-->
<!--                    filterable: true,-->
<!--                    filter: this.selectFilter,-->
<!--                    type: 'select'-->
<!--                },-->
<!--                {-->
<!--                    text: 'Created at',-->
<!--                    value: 'created_at',-->
<!--                    align: 'start',-->
<!--                    filterable: true,-->
<!--                    filter: this.datetimeFilter,-->
<!--                    type: 'datetime',-->
<!--                },-->
<!--                {text: 'Actions', align: 'end', value: 'actions', sortable: false, width: "100px",},-->
<!--            ]-->
<!--        },-->
<!--        searchable() {-->
<!--            return this.headers.filter((item) => item.filterable).map((item) => item.value)-->
<!--        }-->
<!--    },-->
<!--    watch: {-->
<!--        options: {-->
<!--            handler() {-->
<!--                this.getDataFromApi()-->
<!--            },-->
<!--            deep: true,-->
<!--        },-->
<!--        search() {-->
<!--            this.options.page = 1;-->
<!--            this.getDataFromApi()-->
<!--        },-->
<!--        dialogDelete(val) {-->
<!--            val || this.closeDelete()-->
<!--        },-->
<!--        value(val) {-->
<!--            this.selectedHeaders = val;-->
<!--        }-->
<!--    },-->
<!--    methods: {-->
<!--        async getDataFromApi() {-->
<!--            this.loading = true-->
<!--            let {sortBy, sortDesc, page, itemsPerPage} = this.options-->
<!--            let params = {-->
<!--                params: {-->
<!--                    sortBy: sortBy !== undefined ? sortBy[0] : [],-->
<!--                    sortDesc: sortDesc !== undefined ? sortDesc[0] : [],-->
<!--                    page: page,-->
<!--                    itemsPerPage: itemsPerPage,-->
<!--                    query: this.search,-->
<!--                    filter: this.filters,-->
<!--                    search: this.searchable-->
<!--                }-->
<!--            }-->
<!--            let response = await axios.get(`${this.apiUrl}/table`, params);-->
<!--            this.users = response.data.data-->
<!--            this.totalUsers = response.data.meta.total-->
<!--            this.loading = false-->
<!--        },-->
<!--        editItem(item) {-->
<!--            this.editedIndex = this.users.indexOf(item)-->
<!--            this.editedItem = Object.assign({}, item)-->
<!--            this.dialog = true-->
<!--        },-->
<!--        deleteItem(item = null) {-->
<!--            if(item && this.checkItem(item)){-->
<!--                this.selected.push(item)-->
<!--                this.selected.forEach((item) => this.selectedIndexes.push(this.users.indexOf(item)))-->
<!--            }-->
<!--            this.dialogDelete = true-->
<!--        },-->
<!--        checkItem(item){-->
<!--            let pointer = true;-->
<!--            this.selected.forEach((e) => {-->

<!--                if(e.id === item.id){-->
<!--                    pointer = false;-->
<!--                }-->
<!--            })-->
<!--            return pointer;-->
<!--        },-->
<!--        async deleteItemConfirm(single = true) {-->
<!--            let self = this;-->
<!--            let data = this.selected.map((item) => item.id);-->
<!--            if (single) {-->
<!--                await axios.delete(`${this.apiUrl}/${data}`).then(function (response) {-->
<!--                    self.selectedIndexes.forEach((item) => self.users.splice(item, 1))-->
<!--                })-->
<!--            } else {-->
<!--                await axios.post(`${this.apiUrl}/delete-many`, {ids: [...data], _method: 'DELETE'})-->
<!--                    .then(function (response) {-->
<!--                        self.selectedIndexes.forEach((item) => self.users.splice(item, 1))-->
<!--                    })-->
<!--            }-->
<!--            await this.getDataFromApi()-->
<!--            this.closeDelete()-->
<!--        },-->
<!--        selectFilter(value) {-->

<!--            // If this filter has no value we just skip the entire filter.-->
<!--            if (!this.filters.role) {-->
<!--                return true;-->
<!--            }-->
<!--            // Check if the current loop value-->
<!--            // equals to the selected value at the <v-select>.-->
<!--            return value === this.filters.role;-->
<!--        },-->
<!--        datetimeFilter(value) {-->

<!--            // If this filter has no value we just skip the entire filter.-->
<!--            if (!this.filters.created_at) {-->
<!--                return true;-->
<!--            }-->
<!--            // Check if the current loop value-->
<!--            // equals to the selected value at the <v-select>.-->
<!--            return value === this.filters.created_at;-->
<!--        },-->
<!--        closeDelete() {-->
<!--            this.dialogDelete = false-->
<!--            this.$nextTick(() => {-->
<!--                this.selected = []-->
<!--                this.selectedIndexes = []-->
<!--            })-->
<!--        },-->
<!--        reset() {-->
<!--            for (let [key] of Object.entries(this.filters)) {-->
<!--                this.filters[key] = ''-->
<!--            }-->
<!--            this.search = ''-->
<!--            this.$refs.tableDatepicker[0].hide();-->
<!--            this.$refs.tableDatepicker[0].clearDates();-->

<!--            this.getDataFromApi()-->
<!--        },-->
<!--        filter() {-->
<!--            this.options.page = 1;-->
<!--            this.getDataFromApi()-->
<!--        },-->
<!--        filterDatepicker() {-->
<!--            this.filter()-->
<!--            this.$refs.tableDatepicker[0].hide();-->
<!--        },-->
<!--        async getRoles() {-->
<!--            let response = await axios.get(`/api/admin/roles`)-->
<!--            this.roles = response.data.data-->
<!--            this.roles.unshift({'id': null,'name': 'All'})-->
<!--        },-->
<!--    },-->
<!--    created() {-->
<!--        this.selectedHeaders = this.headers;-->
<!--        this.getRoles()-->
<!--        this.getDataFromApi()-->
<!--    },-->

<!--}-->
<!--</script>-->
<!--<style lang="scss">-->
<!--.no-bg-hover::before {-->
<!--    background-color: transparent !important;-->
<!--}-->
<!--table tbody .v-data-table__empty-wrapper {-->
<!--  height: 340px!important;-->
<!--}-->
<!--table tbody {-->
<!--   padding-bottom: 340px;-->
<!--}-->
<!--</style>-->


