<template>
        <div class="flex flex-wrap my-2 p-0 border-0" :class="{'opacity-25' : disabled}">
            <label :for="`input${label}`" class="mb-1"
                  > {{label}} <span v-if="asterisk" class="text-red-600">{{asterisk}}</span></label>
            <input :disabled="disabled" @input="updateModel" :type="type"  v-model="model" :class="this.class +  (error ? ' border border-red-600' : '')"
                   :id="`input${label}`"
                   />
        </div>
        <div class="text-red-600 text-sm" v-if="error" v-html="error" />
</template>
<script>


export default {
    name: "InputComponent",
    props: {
        target: {
            type: Function,
            required: true
        },
        type: {
            type : String,
            default : 'text'
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
        hasError :{
            type: [Boolean]
        },
        asterisk: {},
        error : {},
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
    methods: {
        updateModel(event) {
            this.$emit("update:modelValue", event.target.value);
            this.target(event.target.value);
        }
    }
}
</script>

