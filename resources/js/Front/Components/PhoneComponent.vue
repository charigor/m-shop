<template>
    <div class="flex flex-wrap my-2 p-0 border-0" :class="{'opacity-25' : disabled}">
        <label :for="`input${label}`" class="mb-1"
              >{{label}} <span v-if="asterisk" class="text-red-600">{{asterisk}}</span></label>
        <input :disabled="disabled" type="text"   @accept:masked="onAccept" masked=true v-mask="'38 (0##) ###-##-##'"  @input="updateModel" v-model="model"
               :class="this.class +  (error ? ' border border-red-600' : '')"
               :id="`input${label}`"
        />
    </div>
    <div class="text-red-600 text-sm" v-if="error" v-html="error" />
</template>
<script>

import {IMaskComponent} from 'vue-imask';
import {mask} from 'vue-the-mask'
export default {
    name: "PhoneComponent",
    directives: {mask},
    components: {
        'imask-input': IMaskComponent,


    },

    methods: {
        onAccept (e) {
            console.log(e.length)
            // const maskRef = e.detail;
            // this.value = maskRef.value;
            // console.log('accept', maskRef.value);
        },
        updateModel(event) {
            this.$emit("update:modelValue", event.target.value);
            this.target(event.target.value);
        }
    },
    props: {
        target: {
            type: Function,
            required: true
        },
        type: {
            type: String,
            default: 'text'
        },
        modelValue: {type: [String]},
        label: {type: String},
        disabled: {type: [Boolean]},
        mask: {
            type: String
        },
        class: {
            type: [String, null],
            default: null
        },
        asterisk: {},
        error : {}
    },
    computed: {
        model: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
                this.$emit("update:bindInput", value);
            },
        }
    },
}
</script>

