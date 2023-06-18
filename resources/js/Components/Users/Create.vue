<template>
    <v-flex>
        <template>
            <v-toolbar class="mb-2" elevation="2">
                <v-btn
                    class="mr-2"
                    color="secondary"
                    dark
                    small
                    @click="$router.push('/admin/users')"
                >
                    <v-icon
                        dark
                        left
                    >
                        mdi-arrow-left
                    </v-icon>Back
                </v-btn>
                <v-toolbar-title>{{title}}</v-toolbar-title>
            </v-toolbar>
            <v-divider
                inset
                vertical
            ></v-divider>
        </template>
        <template>
            <v-form v-model="valid"  @submit.prevent="submit">
                <v-container fluid>
                    <v-row>
                        <v-col
                            cols="12"
                            md="12"
                        >
                            <v-text-field
                                v-model="form['name']"
                                :counter="30"
                                label="First name"
                                required
                                :error-messages="errors.name"
                            ></v-text-field>
                        </v-col>

                        <v-col
                            cols="12"
                            md="12"
                        >
                            <v-text-field
                                v-model="form['email']"
                                :error-messages="errors.email"
                                label="E-mail"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col>
                            <UploadImage></UploadImage>
                        </v-col>
                        <v-col  cols="12" md="12">
                            <v-select :error-messages="errors.roles" v-model="form.roles"  multiple :items="roles" item-value="id" item-text="name" label="Roles"/>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="12"
                        >
                            <v-text-field
                                :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="show_password ? `text` :`password`"
                                name="password"
                                label="Password"
                                :counter="8"
                                hint="At least 8 characters"
                                v-model="form['password']"
                                :error-messages="errors.password"
                                class="input-group--focused"
                                @click:append="show_password = !show_password"
                            ></v-text-field>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="12"
                        >
                            <v-text-field
                                :append-icon="show_password_confirmation ? 'mdi-eye' : 'mdi-eye-off'"
                                :type="show_password_confirmation ? `text` :`password`"
                                name="password_confirmation"
                                label="Password Confirmation"
                                :counter="8"
                                hint="At least 8 characters"
                                v-model="form['password_confirmation']"
                                class="input-group--focused"
                                :error-messages="errors.password_confirmation"
                                @click:append="show_password_confirmation = !show_password_confirmation"
                            ></v-text-field>
                        </v-col>
                        <v-col
                            class="d-flex justify-end"
                            cols="12"
                            md="12"
                        >
                            <v-btn
                                class="primary"
                                type="submit"
                                @click.prevent="submit"
                            >
                                submit
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </template>
    </v-flex>
</template>
<script>
import {mapActions} from "vuex";

export default {
    data: () => ({
        apiUrl: '/api/admin/users',
        title: 'Create User',
        valid: false,
        errors: {},
        form: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        },
        loading: true,
        roles: [],
        show_password: false,
        show_password_confirmation: false
    }),
    methods: {
        ...mapActions([
            'SHOW_NOTIFICATION'
        ]),
        async getRoles() {
            let response = await axios.get(`/api/admin/roles`)
            this.roles = response.data.data
        },
        async submit() {
            try {
                await axios.post(`${this.apiUrl}`, this.form)
                Object.keys(this.form).forEach(key => {
                    delete this.form[key];
                });
                this.SHOW_NOTIFICATION({message: 'User was created successfully!','title': '',type: 'success'})
                if (this.$route.path !== '/admin/users')  return this.$router.push('/admin/users')
            } catch (error) {
                this.errors = error.response.data.errors
                this.SHOW_NOTIFICATION({message: 'Something went wrong!','title': 'Error',type: 'error'})
            }

        }
    },
    created() {
        this.getRoles()
    }

}
</script>
