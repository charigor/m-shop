<script>
import { computed, ref } from "vue";
import { useMainStore } from "@/stores/main";
import { mdiEye, mdiTrashCan } from "@mdi/js";
import CardBoxModal from "@/Components/Partials/CardBoxModal.vue";
import TableCheckboxCell from "@/Components/Partials/TableCheckboxCell.vue";
import BaseLevel from "@/Components/Partials/BaseLevel.vue";
import BaseButtons from "@/Components/Partials/BaseButtons.vue";
import BaseButton from "@/Components/Partials/BaseButton.vue";
import UserAvatar from "@/Components/Partials/UserAvatar.vue";
export default {
    props : ['data'],
    data:() => ({

    }),
    methods:{

    }
}
</script>

<template>
    <button class="v-btn--size-large" @click="this.$emit('modify','toto')">change</button>
    <slot name="create"></slot>
  <table v-if="data.length">
    <thead >
      <tr>
        <th />
        <th>Name</th>
        <th>Email</th>
        <th>Created</th>
        <th />
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in data" :key="item.id">
        <td class="border-b-0 lg:w-6 before:hidden">
        </td>
        <td data-label="Name">
          {{ item.name }}
        </td>
        <td data-label="Company">
          {{ item.email }}
        </td>
        <td data-label="City">
          {{ item.created_at }}
        </td>
      </tr>
    </tbody>
  </table>

</template>
