<template >
    <div class="ml-4"     >
        <ul class="mb-2" >
            <li >
                <span>
                    <input type="radio" name="parent" v-model="p"   :value="value" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"> {{props.label}}</label>
                </span>
            </li>
        </ul>

            <TreeMenu
                v-for="node in nodes"
                :nodes="node.children"
                :label="node.translation[0].title"
                :value="node.id"
                :parent="parent"
            >
            </TreeMenu>

    </div>
</template>
<script setup>
import {defineProps, defineEmits,ref} from "vue";
const emit = defineEmits(['setParent'])
const props = defineProps({
    label: {
        type: String,
    },
    nodes: {
        type: Object,
    },
    value:{
        default: 0
    },

    parent:{}


});
    const p = ref(props.parent);
</script>
