<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  type: {
    type: String,
    default: "td",
  },
    value: {
        type: Object,
    },
});

const emit = defineEmits(["checked"]);

const checked = ref(false);

watch(checked, (newVal) => {
  emit("checked", newVal,props.value);
});
watch(checked, (newVal) => {
    emit("checkedAll");
});
</script>

<template>
  <component :is="type" class="lg:w-1">
    <label class="checkbox">
      <input v-model="checked" type="checkbox" />
      <span class="check" />
    </label>
  </component>
</template>
