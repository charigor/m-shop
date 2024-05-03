<template>

        <li class="flex flex-col">
            <div class="flex items-center justify-between w-full py-2 border-b">
                <div class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0">
                                    1
                                </span>
                    Персональна інформація
                </div>
                <span class="text-blue-500 cursor-pointer" v-if="step > 1" @click="nextStep(1)">редагувати</span>
            </div>
            <transition name="slide">
                <div v-show="step === 1" class="p-2 border">
                    <div v-if="step === 1" class="w-full p-2 mb-2 border-blue-600">
                        <div class="flex w-full mb-5">
                            <div v-for="option in userTypeOptions" :key="option.id">
                                <template>
                                    <input type="radio" :id="`user_type_${option.id}`" class="hidden peer"
                                           :value="option.id" @change="updateUserType(option.id)" name="userType"/>
                                    <label :for="`user_type_${option.id}`"
                                           class="cursor-pointer  mr-2 p-2 peer-checked:bg-gray-600 peer-checked:text-gray-200">
                                        {{ option.text }}</label>
                                </template>
                            </div>
                        </div>
                        <div v-if="userType === 'user'">
                            <input-component v-model="user.name" type="text" label="Ім'я" class="w-full" name="Name"
                                             required></input-component>
                            <input-component v-model="user.lastname" type="text" label="Прізвище" class="w-full"
                                             name="Lastname" required></input-component>
                            <phone-component v-model="user.phone" type="text" label="Телефон" class="w-full" name="Phone"
                                             required></phone-component>
                            <input-component v-model="user.email" type="email" label="Email" class="w-full" name="Email"
                                             required></input-component>
                            <div class="flex justify-end">
                                <BaseButton
                                    @click="nextStep(2)"
                                    label="Далі"
                                    class="bg-blue-800"
                                />
                            </div>
                        </div>
                        <div v-else>
                            <template v-if="!auth">
                                <input-component v-model="user.email" type="email" label="Email" class="w-full"
                                                 name="Email"></input-component>
                                <input-component v-model="user.password" type="password" label="Password" class="w-full"
                                                 name="Password"></input-component>
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
            </transition>
        </li>
        <li class="flex flex-col">
            <div class="flex items-center justify-between w-full py-2 border-b">
                <div class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0">
                                    2
                                </span>
                    Доставка
                </div>
                <span class="text-blue-500 cursor-pointer" v-if="step > 2" @click="nextStep(2)">редагувати</span>
            </div>
            <transition name="slide">
                <div v-show="step === 2" class="p-2 border">
                    <div class="w-full" v-show="step === 2">
                        <city-component url="/novaposhta/cities" class="mb-5" label="Present" option-name="Addresses"
                                        @selected="(value) => this.city = value" total-name="TotalCount"
                                        ref="citySelect"></city-component>
                        <div v-if="city">
                            <div class="flex w-full mb-5 p-2 border-b">
                                <div v-for="option in deliveryTypeOptions" :key="option.id">
                                    <input type="radio" :id="`delivery_type_${option.id}`" class="hidden peer"
                                           :value="option.id" @change="updateDeliveryType(option.id)" name="deliveryType"/>
                                    <label :for="`delivery_type_${option.id}`"
                                           class="cursor-pointer p-2  mr-2 border peer-checked:bg-gray-600 peer-checked:text-gray-200">
                                        {{ option.text }}</label>
                                </div>
                            </div>
                            <div v-show="deliveryType === 3">
                                <warehouse-type-component url="/novaposhta/warehouseTypes" class="mb-2" label="Description"
                                                          :city="city" @selected="(value) => this.warehouseType = value"
                                                          ref="warehouseTypeSelect"></warehouse-type-component>
                                <warehouse-component url="/novaposhta/warehouses" class="mb-2" :city="city"
                                                     :type="warehouseType" label="Description" option-name="Description"
                                                     @selected="(value) => this.warehouse = value" total-name="TotalCount"
                                                     ref="warehouseSelect"></warehouse-component>
                            </div>
                        </div>
                        <div class="flex justify-end" v-if="step === 2">
                            <BaseButton
                                @click="nextStep(3)"
                                label="Далі"
                                class="bg-blue-800"
                            />
                        </div>
                    </div>
                </div>
            </transition>
        </li>
        <li class="flex flex-col">
            <div class="flex items-center justify-between w-full py-2 border-b">
                <div class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0">
                                    3
                                </span>
                    Платіж
                </div>
                <span class="text-blue-500 cursor-pointer" v-if="step > 3" @click="nextStep(3)">редагувати</span>
            </div>

            <div class="w-full" v-show="step === 3">
                Платежі
                <transition name="slide">
                    <div v-if="step === 3" class="p-2 border">
                        <div class="flex justify-end" v-if="step === 2">
                            <BaseButton
                                @click="nextStep(4)"
                                label="Створити платіж"
                                class="bg-blue-800"
                            />
                        </div>
                    </div>
                </transition>
            </div>
        </li>

</template>
<script>
import InputComponent from "@/Front/Components/InputComponent.vue";
import PhoneComponent from "@/Front/Components/PhoneComponent.vue";
import CityComponent from "@/Front/Components/CityComponent.vue";
import WarehouseComponent from "@/Front/Components/WarehouseComponent.vue";
import SelectComponent from "@/Front/Components/SelectComponent.vue";
import WarehouseTypeComponent from "@/Front/Components/WarehouseTypeComponent.vue";
import BaseIcon from "@/Front/Components/UI/BaseIcon.vue";
import {mdiGoogle, mdiFacebook} from "@mdi/js";
import BaseButton from "@/Front/Components/UI/BaseButton.vue";
export default {
    name: "OrderStep",
    components: {
        BaseButton,
        BaseIcon,
        WarehouseTypeComponent,
        PhoneComponent,
        InputComponent,
        CityComponent,
        WarehouseComponent,
        SelectComponent,
    },
    data() {
        return {
            warehouse : null,
            warehouseType : null,
        };
    },
    props: {
        key: {
            type: Number
        },
      step: {
          type: Number,
          default: 1
      },
        currentStep: {
            type: Number,
            default: 1
        },
        user: {
            type: Object,
        },
        auth: {
            type: Boolean,
            default: false
        },
        city: {
            type: Object,
        },
        deliveryTypeOptions: {
            type: Array,
        },
        deliveryType: {
            type: Number,
        },
        userTypeOptions: {
            type: Array,
        },
        userType: {
            type: String,
            default: 'user'
        },
    },
    methods : {
        updateUserType(value) {
            this.$emit('update:userType', value); // Emit an event to update userType prop in the parent component
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
        nextStep(step) {
            this.currentStep = step;
            if (step === 1) this.userType = "user";
        },
    },
    mounted() {
        const [firstDelivery] = [...this.deliveryTypeOptions]
        this.deliveryType  = firstDelivery?.id
        if(!this.auth) this.userType = 'user'
    }
};
</script>



<style scoped>

</style>
