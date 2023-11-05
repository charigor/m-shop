<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="500px"
    >
        <template v-slot:activator="{ props }">
            <v-btn
                class="m-auto"

                small
                v-bind="props"
            >
                <v-icon aria-hidden="false" small>
                    mdi-format-indent-increase
                </v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-card-title>Select Fields</v-card-title>
            <v-divider></v-divider>
            <v-card-text style="height: 500px;">

                <v-checkbox v-for="(item,index) in fields.filter((item) => item.key !== 'actions')" :key="index"
                            :label="item.title"
                            :value="item"
                            v-model="newheaders"

                ></v-checkbox>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="d-flex justify-end">

                <v-btn
                    color="blue darken-1"
                    text
                    @click="dialog = false"
                >
                    Close
                </v-btn>
                <v-btn
                    color="blue darken-1"
                    text
                    @click="apply()"
                >
                    Apply
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>
<script>
export default {
    props: ['fields', 'headers','store_name'],
    data() {
        return {
            dialog: false,
            newheaders: this.headers.map(item => {
                if(item.filter != undefined) delete item.filter
                  return item
            })
        }
    },
    methods: {

        apply() {
            this.newheaders = this.fields.filter((item) => {
                return (item.key === 'actions' || this.newheaders.find((element) => {
                    return element.key === item.key
                }))
            });
            localStorage.setItem(this.store_name, JSON.stringify(this.newheaders));
            this.$emit('apply', this.newheaders);
            this.dialog = false;
            this.getDataFromApi()
        }
    },
    created() {
        // props are exposed on `this`
    }
}
</script>
