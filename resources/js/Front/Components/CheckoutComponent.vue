<template>
    <div>
        <ol class="flex flex-col">
            <!-- USER INFO -->
            <li class="flex flex-col">
                <div class="flex items-center justify-between w-full py-2 border-b">
                    <div class="flex items-center" :class="{ 'text-green-600' : (step > 1) }">
                        <span class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0" :class="{'text-green-600 border-green-600' : (step > 1)}">
                            1
                        </span>
                        Персональна інформація
                    </div>
                    <span class="text-green-600 cursor-pointer text-xs"  v-if="step > 1" @click="back(1)">редагувати</span>
                </div>
                <user-info-component :state="step === 1" :form="form" @update="updateFormData" :next="next"/>
            </li>
            <li class="flex flex-col">
                <div class="flex items-center justify-between w-full py-2 border-b">
                    <div class="flex items-center">
                        <span class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0">
                            2
                        </span>
                        Доставка
                    </div>
                    <span class="text-green-600 cursor-pointer text-xs" v-if="step > 2" @click="back(2)">редагувати</span>
                </div>
                <transition name="slide">
                    <div  v-if="step === 2" class="p-2 border">
                        <div class="w-full">
                            <select2-city-component url="/novaposhta/cities" class="mb-5" :value="city" ref="citySelect" @selected="(value) => this.city = value"/>
                            <div v-if="city">
                                <div class="flex w-full mb-5 p-2 border-b">
                                    <div v-for="option in deliveryTypeOptions" :key="option.id">
                                          <input type="radio" :id="`delivery_type_${option.id}`" class="hidden peer" :value="option.id"  v-model="deliveryType" name="deliveryType"/>
                                          <label :for="`delivery_type_${option.id}`" class="cursor-pointer p-2  mr-2 border peer-checked:bg-gray-600 peer-checked:text-gray-200"> {{option.text}}</label>
                                    </div>
                                </div>
                                <div v-if="deliveryType === 3">
                                    <select2-warehouse-type-component :value="warehouseType" class="mb-2" :city="city" @selected="(value) => this.warehouseType = value" ref="warehouseTypeSelect"></select2-warehouse-type-component>
                                    <select2-warehouse-component :value="warehouse" class="mb-2" :city="city" :type="warehouseType"  @selected="(value) => this.warehouse = value"  ref="warehouseSelect"></select2-warehouse-component>
                                </div>
                            </div>
                        <div class="flex justify-end" v-if="step === 2">
                            <BaseButton
                                @click="next(3)"
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
                        <span class="flex items-center justify-center w-5 h-5 me-2 text-xs border border-gray-600 rounded-full shrink-0">
                            3
                        </span>
                        Платіж
                   </div>
                    <span class="text-blue-500 cursor-pointer" v-if="step > 3" @click="back(3)">редагувати</span>
                </div>

                <div class="w-full" v-show="step === 3">
                Платежі
                <transition name="slide">
                    <div v-if="step === 3" class="p-2 border">
                        <div class="flex justify-end" v-if="step === 3">
                            <BaseButton
                                @click="next(4)"
                                label="Створити платіж"
                                class="bg-blue-800"
                            />
                        </div>
                    </div>
                </transition>
        </div>
            </li>
        </ol>
    </div>
</template>
<script>

import InputComponent from "@/Front/Components/InputComponent.vue";
import PhoneComponent from "@/Front/Components/PhoneComponent.vue";
import WarehouseComponent from "@/Front/Components/WarehouseComponent.vue";
import SelectComponent from "@/Front/Components/SelectComponent.vue";
import BaseIcon from "@/Front/Components/UI/BaseIcon.vue";
import BaseButton from "@/Front/Components/UI/BaseButton.vue";
import Form from 'vform'
import UserInfoComponent from "@/Front/Components/Checkout/UserInfoComponent.vue";
import Select2CityComponent from "@/Front/Components/Checkout/Select2CityComponent.vue";
import Select2WarehouseTypeComponent from "@/Front/Components/Checkout/Select2WarehouseTypeComponent.vue";
import Select2WarehouseComponent from "@/Front/Components/Checkout/Select2WarehouseComponent.vue";

export default {
    components: {
        Select2WarehouseComponent,
        Select2CityComponent,
        Select2WarehouseTypeComponent,
        Form,
        BaseButton,
        BaseIcon,
        PhoneComponent,
        InputComponent,
        WarehouseComponent,
        SelectComponent,
        UserInfoComponent
    },
    data() {
        return {
            auth: window.AUTH,
            form: new Form({
                name: null,
                lastname: null,
                phone: null,
                email: null,
                password: ''
            }),
            step: 1,
            city : null,
            warehouse : null,
            warehouseType : null,
            deliveryType: null,
        };
    },
    computed : {
        deliveryTypeOptions () {
            return  window.DELIVERY_TYPE_OPTIONS
        }
    },
    methods : {

        async next(step)
        {
            if(step === 2){
                const response = await this.form.post('checkout/stepOne')
                if(response.data.success){
                    this.updateStepProgress(step)
                    this.step = step
                }
            }
            if(step === 3){
                this.updateStepProgress(step)
                this.step = step
            }

        },
        back(step){
            this.step = step
        },
        updateStepProgress(step) {
            localStorage.removeItem('checkoutProgress');
            if(step === 1){
                this.currentStep  = {step : step}
            }
            if(step === 2){
                this.currentStep  = this.form
                this.currentStep.step = step
            }
            if(step === 3){
                this.currentStep.deliveryType = this.deliveryType
                this.currentStep.city = this.city
                this.currentStep.warehouse = this.warehouse
                this.currentStep.warehouseType = this.warehouseType
                this.currentStep.step = step
            }
            localStorage.setItem('checkoutProgress', JSON.stringify(this.currentStep));
        },
        updateFormData(updatedData) {
            for (let key in updatedData) {
                if (this.form.hasOwnProperty(key)) {
                    this.form[key] = updatedData[key];
                }
            }
        }
    },
    mounted() {
        let checkoutProgress = localStorage.getItem('checkoutProgress') ? JSON.parse(localStorage.getItem('checkoutProgress')) :  {};
        this.form.name = window.USER?.name ?? checkoutProgress?.name
        this.form.lastname = window.USER?.phone ?? checkoutProgress?.lastname
        this.form.email = window.USER?.email ?? checkoutProgress?.email
        this.form.phone = window.USER?.phone ?? checkoutProgress?.phone
        this.city = checkoutProgress?.city ?? null
        this.warehouseType = checkoutProgress?.warehouseType ?? null
        this.warehouse = checkoutProgress?.warehouse ?? null
        this.deliveryType = checkoutProgress?.deliveryType
        this.step = checkoutProgress?.step ?? 1

    }

};
</script>
<style>
.form-group.error label{
    color: rgb(220, 38, 38);
}
.form-group.error input{
    border: 1px solid rgb(220, 38, 38);
}
</style>
