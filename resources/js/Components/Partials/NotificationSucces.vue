<script setup>
import { ref, computed, useSlots,reactive } from "vue";
import { mdiClose } from "@mdi/js";
import { usePage } from '@inertiajs/vue3'
import { colorsBgLight, colorsOutline } from "@/colors";
import BaseLevel from "@/Components/Partials/BaseLevel.vue";
import BaseIcon from "@/Components/Partials/BaseIcon.vue";
import BaseButton from "@/Components/Partials/BaseButton.vue";

const props = defineProps({
  icon: {
    type: String,
    default: null,
  },
  outline: Boolean,
  color: {
    type: String,
    required: true,
  },

});


const componentClass = computed(() =>
  props.outline ? colorsOutline[props.color] : colorsBgLight[props.color]
);




const slots = useSlots();

const hasRightSlot = computed(() => slots.right);
const show = ref(usePage().props.flash.message);
</script>

<template>

  <div
    :class="componentClass"
    class="px-3 py-6 md:py-3 mb-6 last:mb-0 border rounded-lg transition-colors duration-150"
  >
    <BaseLevel>
      <div class="flex flex-col md:flex-row items-center">
        <BaseIcon
          v-if="icon"
          :path="icon"
          w="w-10 md:w-5"
          h="h-10 md:h-5"
          size="24"
          class="md:mr-2"
        />
        <span class="text-center md:text-left md:py-2"><slot /></span>
      </div>
      <slot v-if="hasRightSlot" name="right" />
      <BaseButton
        v-else
        :icon="mdiClose"
        small
        rounded-full
        color="white"
        @click="$page.props.flash.message = ''"
      />
    </BaseLevel>

  </div>

</template>
