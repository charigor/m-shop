<script>

import BaseIcon from "@/Front/Components/UI/BaseIcon.vue";
export default {
    components: {
        BaseIcon
    },
    name: 'BaseButton',
    props: {
        label: {
            type: [String, Number],
            default: null,
        },
        icon: {
            type: String,
            default: null,
        },
        iconSize: {
            type: [String, Number],
            default: null,
        },
        small: Boolean,
        outline: Boolean,
        active: Boolean,
        disabled: Boolean,
        roundedFull: Boolean,
    },
    computed: {
        labelClass: function() {
            this.small && this.icon ? 'px-1' : 'px-2'
        },
        computedClass: function() {
            const base = [
                'inline-flex',
                'justify-center',
                'items-center',
                'whitespace-nowrap',
                'focus:outline-none',
                'transition-colors',
                'focus:ring',
                'duration-150',
                'border',
                'text-white',
                this.disabled ? 'cursor-not-allowed' : 'cursor-pointer',
            ];

            if (!this.label && this.icon) {
                base.push('p-1');
            } else if (this.small) {
                base.push('text-sm', this.roundedFull ? 'px-3 py-1' : 'p-1');
            } else {
                base.push('py-2', this.roundedFull ? 'px-6' : 'px-3');
            }

            if (this.disabled) {
                base.push(this.outline ? 'opacity-50' : 'opacity-70');
            }

            return base;
        }
    }
}


</script>

<template>
    <button
        :class="computedClass"
        :disabled="disabled"
    >
        <BaseIcon v-if="icon" :path="icon" class="mr-1" :size="iconSize" />
        <span v-if="label" :class="labelClass">{{ label }}</span>
    </button>
</template>
