<script>
export default {
    name: "BaseIcon",
    props: {
        path: {
            type: String,
            required: true,
        },
        w: {
            type: String,
            default: "w-6",
        },
        h: {
            type: String,
            default: "h-6",
        },
        size: {
            type: [String, Number],
            default: null,
        },
    },
    computed: {
        spanClass: function () {
            return `inline-flex justify-center items-center ${this.w} ${this.h}`
        },
        iconSize: function () {
            return this.size ?? 16
        }
    }
}
</script>

<template>
  <span :class="spanClass">
    <svg
        viewBox="0 0 24 24"
        :width="iconSize"
        :height="iconSize"
        class="inline-block"
    >
      <path fill="currentColor" :d="path"/>
    </svg>
    <slot/>
  </span>
</template>
