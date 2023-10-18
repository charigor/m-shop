<script setup>
import {computed, defineProps, reactive, ref} from "vue";
import {useMainStore} from "@/stores/main.js";

const props = defineProps({
  attributes: {
    type: Object,
  }
});
const locale = ref(useMainStore().lang)
const form = reactive([]);
const attribute = reactive([]);

const generateCombination = () => {
  emit('addAttributes', attr)
}

const addAttribute = (id) => {
  form.push(id)
}
const attr = computed(() => {
  return form.filter((val, i) => form.indexOf(val) === i);
})
</script>

<template>

<div class="col-span-3">
  <button type="button" @click="generateCombination" class="inline-block p-4 bg-gray-300 dark:bg-blue-600  hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" >
    Generate
  </button>
</div>
<div class="col-span-1">
  <ul  class="mb-5" v-for="attributeGroup in props.attributes" :key="attributeGroup.id">
    {{attributeGroup.translate.name}}
    <li  class="my-4 border p-5" v-for="attribute in attributeGroup.attributes" :key="attribute.id">
      <div class="flex items-center my-2" @click="addAttribute(attribute.id)" ><span v-if="attributeGroup.is_color_group" class="inline-block w-[20px] h-[20px] mr-2" :style="{'backgroundColor': `${attribute.color}`}"></span>{{attribute.translate.name}}</div>
    </li>
  </ul>
</div>



<!--    <table>-->
<!--      <thead >-->
<!--      <tr>-->
<!--        <th />-->
<!--        <th>Image</th>-->
<!--        <th>Email</th>-->
<!--        <th>Created</th>-->
<!--        <th />-->
<!--      </tr>-->
<!--      </thead>-->
<!--      <tbody>-->
<!--      <tr v-for="item in data" :key="item.id">-->
<!--        <td class="border-b-0 lg:w-6 before:hidden">-->
<!--        </td>-->
<!--        <td data-label="Name">-->
<!--          {{ item.name }}-->
<!--        </td>-->
<!--        <td data-label="Company">-->
<!--          {{ item.email }}-->
<!--        </td>-->
<!--        <td data-label="City">-->
<!--          {{ item.created_at }}-->
<!--        </td>-->
<!--      </tr>-->
<!--      </tbody>-->
<!--    </table>-->

<!--    <div v-for="language in $page.props.languages" :key="language.id">-->
<!--      <div class="relative z-0 w-full mb-6 group" v-show="locale === language.code">-->

<!--      </div>-->
<!--    </div>-->

</template>

<style scoped>

</style>
