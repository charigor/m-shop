<script setup>
import {computed, defineProps, reactive, defineEmits, ref, watch, onMounted} from "vue";
import {useMainStore} from "@/stores/main.js";
import BaseButtons from "@/Components/Partials/BaseButtons.vue";
import BaseLink from "@/Components/Partials/BaseLink.vue";
import BaseButton from "@/Components/Partials/BaseButton.vue";
import {mdiCircleEditOutline, mdiTrashCan} from "@mdi/js";
import Checkbox from '@/Components/Partials/UI/Checkbox.vue'
import CardBoxModal from "@/Components/Partials/CardBoxModal.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import Radio from "@/Components/Partials/UI/Radio.vue";
import Label from "@/Components/Partials/UI/Label.vue";
const props = defineProps({
  attributes: {
    type: Object,
  },
  productAttributes: {
    type: Object,
  },
  images: {
    type: Object
  }
});
const form = useForm([]);
const locale = ref(useMainStore().lang)
const attr = ref([]);
const defaultAttr = ref('');
const captionObject = ref({})
let groups = ref([]);
const isModalDangerActive = ref(false);
const attribute = reactive([]);
const emit = defineEmits(["addNewAttribute"]);
const update = (id, elem) => {
  router.put(`/admin/attribute_product/${id}`, elem, {preserveState: false})
}
const generateCombination = () => {
  emit('addNewAttribute', attr)
}
const changeDefaultAttribute = () => {
  emit('changeDefaultAttribute', defaultAttr)
}
const confirmDelete = () => {
  router.delete(`/admin/attribute_product/${captionObject.value.id}`, {preserveState: false});
  captionObject.value = null
}

const isDisabled = computed(() => {
  let result = false;
  if (!attr.value?.length) return true
  groups.value.forEach(function (item) {
    if(item.length !== attr.value.length) {
      result = true;
      return false;
    }
    if (item.every(elem => {
      return attr.value.includes(elem)
    })) {
      result = true;
      return false;
    }
  });
  return result;
})
const isAttrDisabled = (group_id,attr_id) => {
  let result = false;
  if (!attr.value?.length) return false
  attr.value.forEach((item) => {
    let attributes = props.attributes.filter((el) => (el.attributes.find(({ id }) => id === item)))
    if(group_id === attributes[0].id && attr_id !== item) return result = true
  })
  return result;
}
const getPreview = (media) => {
 let result = false;
  for(let index in media)
  {
    if(media[index].mainImage === 1 && media[index].active === "1")
    {
       result = media[index];
    }
  }

  if(result == false){
    for(let index in media)
    {
      if(media[index].active === "1")
      {
        return  result = media[index];
      }
    }
  }
  return result
};
onMounted(() => {
  props.productAttributes.forEach(function (item, index) {
    let arr = item.attributes.map(i => i.id);
    groups.value.push(arr)
  })
  form.value = props.productAttributes.map((item) => ({
    "id": item.id,
    "price": item.price,
    "rebate": item.rebate,
    "quantity": item.quantity,
    "width": item.width,
    "height": item.height,
    "weight" : item.weight,
    "depth": item.depth,
    "attributes": item.attributes,
    "reference" : item.reference,
    "media": item.media.map((i) => ({
      'id': i.id,
      'active': String(i.custom_properties.active),
      'mainImage': i.custom_properties.main_image,
      'previewUrl': i.preview_url,
      'order' : i.order,
      'fileName': i.file_name
    })).sort((a, b) => {
      if (a.order < b.order) return 1;
      if (a.order > b.order) return -1;
      return 0;
    }),

  }))

})
const showDeleteModal = (el) => {
  isModalDangerActive.value = true;
  captionObject.value = el;
}
const checkGroup = (id) => {
  let result = false;
  if (!form.value?.length) return true
  for (let item in form.value) {
    for (let el in form.value[item].attributes) {
      if (form.value[item].attributes[el].attribute_group_id === id) result = true;
    }
  }
  return result;
}
onMounted(() => {
  [defaultAttr.value] = props.productAttributes.filter((item) => item.default === 1).map((v) => v.id)
})
watch(defaultAttr, (newVal) => {
  emit('changeDefaultAttribute', newVal)
})
</script>

<template>
  <div class="col-span-3">
    <div class="border p-2 mb-5" v-if="attr.length">
    <span v-for="attributeGroup in props.attributes">
        <template v-for="attribute in attributeGroup.attributes" :key="attribute.id">
            <span class="bg-blue-600 p-1 mx-1" v-if="attr.includes(attribute.id)">
             {{ attribute.translate.name }}
            </span>
        </template>
    </span>
    </div>
    <div class="flex justify-end">
      <button type="button" @click="generateCombination"
              class="inline-block disabled:opacity-25 p-4 bg-gray-300 dark:bg-blue-600  hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
              :disabled="isDisabled">
        {{$t('global.generate')}}
      </button>
    </div>
    <div class="p-5 border mt-5">
      <table class="table table-auto w-full  dark:bg-gray-700 shadow rounded">
        <thead>
        <tr>
          <th colspan="7">Default</th>
          <th class="pb-4 pt-6 px-6 lg:w-6 whitespace-nowrap justify-start"><p>{{ $t('global.action') }}</p></th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(attribute) in form.value">
          <tr>
            <td>
              <Radio label="" v-model="defaultAttr" :value="attribute.id"/>
            </td>
            <td>
              <div>
                <img  :src="getPreview(attribute.media).previewUrl"
                     :alt="getPreview(attribute.media).fileName" class="h-[50px] w-[50px]">
              </div>
            </td>
            <td>
              <span
                  v-for="(attr,i) in  attribute.attributes">{{
                  attr.translate.name
                }}{{ attribute.attributes.length - 1 !== i ? '-' : '' }}</span>
            </td>
            <td colspan="4"></td>
            <td>
              <template class="flex items-center">
                <BaseButtons type="justify-start lg:justify-start" no-wrap>
                  <BaseLink
                      color="gray"
                      middle
                      href="#"
                      :icon="mdiCircleEditOutline"
                  >
                  </BaseLink>
                  <BaseButton
                      color="danger"
                      :icon="mdiTrashCan"
                      middle
                      @click.prevent="showDeleteModal(attribute)"
                  />
                </BaseButtons>
              </template>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_price" :content="$t(`page.product.blocks.combinations.fields.price`)"/>
              <input id="combo_price"  type="text" v-model="attribute.price"
                       class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_quantity" :content="$t(`page.product.blocks.combinations.fields.quantity`)"/>
              <input id="combo_quantity" type="number" min="0" v-model="attribute.quantity"
                       class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_rebate" :content="$t(`page.product.blocks.combinations.fields.rebate`)"/>
              <input id="combo_rebate" type="text" v-model="attribute.rebate"
                     class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_reference" :content="$t(`page.product.blocks.combinations.fields.reference`)"/>
              <input id="combo_reference"  type="text" v-model="attribute.reference"
                                                class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
          </tr>
          <tr>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_height" :content="$t(`page.product.blocks.combinations.fields.height`)"/>
              <input id="combo_height" type="text" v-model="attribute.height"
                                                class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_width" :content="$t(`page.product.blocks.combinations.fields.width`)"/>
              <input id="combo_width" type="text" v-model="attribute.width"
                                                class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_depth" :content="$t(`page.product.blocks.combinations.fields.depth`)"/>
              <input id="combo_depth" type="text" v-model="attribute.depth"
                                                class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
            <td colspan="2" class="py-4">
              <Label class="text-lg mb-2" for="combo_weight" :content="$t(`page.product.blocks.combinations.fields.weight`)"/>

              <input type="text" id="combo_weight" v-model="attribute.weight"
                                                class="block w-full rounded-md border bg-gray-50 border w-full border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-50">
            </td>
          </tr>
          <tr>
            <td colspan="6" class="py-4">
              <div class="flex flex-wrap w-100">
                <div v-for="media in attribute.media">
                  <div :class="{'border': media.active === '1'}"
                       class="p-2 flex rounded hover:bg-gray-100 dark:hover:bg-gray-600 mr-3">
                    <label class="relative inline-flex items-center w-full cursor-pointer">
                      <input type="checkbox" v-model="media.active" true-value="1" false-value="0"
                             class="sr-only hidden w-0 peer">
                      <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                    <img class="w-[100px]" :src="media.previewUrl"
                         :alt="media.fileName">
                  </span>
                    </label>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="8" class="py-4 mb-5">
              <div class="flex justify-end">
                <button type="button" @click.prevent="update(attribute.id,attribute)"
                        class="right text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                  {{ $t('global.edit') }}
                </button>
              </div>
            </td>
          </tr>
        </template>
        </tbody>
      </table>
    </div>
    <CardBoxModal
        v-model="isModalDangerActive"
        :title="$t('global.please_confirm')"
        button="danger"
        has-cancel
        @confirm="confirmDelete"
    >
    {{$t('global.confirm.messages.delete_item')}}
      <p><b v-if="captionObject"
            v-for="(attr,index) in  captionObject.attributes">{{
          attr.translate.name
        }}{{ captionObject.attributes.length - 1 !== index ? '-' : '' }}</b>
      </p>-
    </CardBoxModal>
  </div>
  <div class="col-span-1">
    <ul class="mb-5" v-for="attributeGroup in props.attributes" :key="attributeGroup.id">
      <template v-if="checkGroup(attributeGroup.id)">
        {{ attributeGroup.translate.name }}
        <li class="my-4 border p-5">
          <template v-for="attribute in attributeGroup.attributes" :key="attribute.id">
            <div class="flex items-center">
              <Checkbox :disabled="isAttrDisabled(attributeGroup.id,attribute.id)" :label="attribute.translate.name" v-model="attr" :value="attribute.id"/>
              <span v-if="attributeGroup.is_color_group" class="inline-block w-[10px] h-[10px] ml-2"
                    :style="{'backgroundColor': `${attribute.color}`}"></span>
            </div>
          </template>
        </li>
      </template>
    </ul>
  </div>
</template>
