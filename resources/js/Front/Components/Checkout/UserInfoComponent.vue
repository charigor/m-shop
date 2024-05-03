<script>
import PhoneComponent from "@/Front/Components/PhoneComponent.vue";
import BaseButton from "@/Front/Components/UI/BaseButton.vue";
import InputComponent from "@/Front/Components/InputComponent.vue";
import {mdiFacebook, mdiGoogle} from "@mdi/js";

export default {
    name: "UserInfoComponent",
    components: {InputComponent, BaseButton, PhoneComponent},
    props: {
        state: Boolean,
        form: {},
        next: Function
    },
    data() {
        return {
            user: window.USER,
            userTypeOptions : [
                {id: 'user',text: "Новий покупець"},
                {id: 'customer',text : 'Постійний кліент'}
            ],
        }
    },
    computed : {
        userType () {
            return  this.user ? 'user' : 'customer';
        },
        localForm() {
            let storage = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
            return {
                name: storage?.name ?? this.localForm?.name ?? this.form?.name,
                email: storage?.email ?? this.localForm?.email ?? this.form?.email,
                phone: storage?.phone ?? this.localForm?.phone  ?? this.form?.phone,
                lastname: storage?.lastname ?? this.localForm?.lastname ?? this.form?.lastname
            }

        }
    },
    methods: {
        updateForm() {
            this.$emit('update', this.localForm);
        },
        mdiGoogle() {
            return mdiGoogle
        },
        mdiFacebook() {
            return mdiFacebook
        },
        socialLogin(driver){
            window.location.href = `/login/${driver}`;
        },
        handleNext() {
            this.next(2);
        }
    },
}
</script>

<template>
    <transition name="slide">
        <div v-show="state" class="p-2 border">
            <div v-if="state" class="w-full p-2 mb-2 border-blue-600">
                <div  class="flex w-full mb-5">
                    <div v-for="option in userTypeOptions" :key="option.id">
                        <template>
                            <input type="radio" :id="`user_type_${option.id}`" class="hidden peer" :value="option.id"  v-model="userType"/>
                            <label :for="`user_type_${option.id}`" class="cursor-pointer  mr-2 p-2 peer-checked:bg-gray-600 peer-checked:text-gray-200"> {{option.text}}</label>
                        </template>
                    </div>
                    <div v-if="userType === 'user'">
                        <input-component  v-model="localForm.name" :target="updateForm" :error="form.errors?.get('name')"  type="text" label="Ім'я" class="w-full"  asterisk="*" ></input-component>
                        <input-component v-model="localForm.lastname" :target="updateForm"  :error="form.errors?.get('lastname')" type="text" label="Прізвище" class="w-full" asterisk="*" ></input-component>
                        <phone-component v-model="localForm.phone" :target="updateForm"  :error="form.errors?.get('phone')" type="text" label="Телефон" class="w-full" asterisk="*" ></phone-component>
                        <input-component v-model="localForm.email" :target="updateForm" :error="form.errors?.get('email')" type="email" label="Email" class="w-full" asterisk="*"></input-component>
                        <div class="flex justify-end">
                            <BaseButton
                                @click="handleNext"
                                label="Далі"
                                class="bg-blue-800"
                            />
                        </div>
                    </div>
                    <div v-else>
                        <template v-if="!user">
                            <input-component v-model="localForm.email" :target="updateForm" :error="form.errors?.get('email')" type="email" label="Email" class="w-full"></input-component>
                            <input-component v-model="localForm.password" :target="updateForm" :error="form.errors?.get('password')"  type="password" label="Password" class="w-full"></input-component>
                        </template>
                        <div>
                            <h2 class="my-2 p-2">Вхід через соціальну мережу</h2>
                            <div class="flex">
                                <BaseButton
                                    @click="socialLogin('google')"
                                    label="Google"
                                    class="bg-yellow-800 flex"
                                    :icon="mdiGoogle()"
                                    :iconSize="20"
                                />
                                <BaseButton
                                    @click="socialLogin('facebook')"
                                    label="Facebook"
                                    class="bg-blue-800 flex"
                                    :icon="mdiFacebook()"
                                    :iconSize="20"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>

</style>
