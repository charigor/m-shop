<template>
    <li>
        <div
            :class="{bold: isFolder}"
            @click="toggle"
            @dblclick="makeFolder">

            <input type="radio" name="parent" @change="$emit('on-change', $event)" :value="item.id" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 bg-red"> {{ item.translation[0].title }}</label>
            <span v-if="isFolder">[{{ isOpen ? '-' : '+' }}]</span>
        </div>
        <ul class="ml-4" v-show="isOpen" v-if="isFolder">
            <tree-item
                class="item"
                v-for="(child, index) in item.children"
                :key="index"
                :item="child"
                @make-folder="$emit('make-folder', $event)"
                @add-item="$emit('add-item', $event)"
            ></tree-item>
<!--            <li class="add" @click="$emit('add-item', item)">+</li>-->
        </ul>
    </li>

</template>
<script>
 export default {
     props: {
         item: Object
     },
     data: function() {
         return {
             isOpen: false
         };
     },
     computed: {
         isFolder: function() {
             return this.item.children && this.item.children.length;
         }
     },
     methods: {
         toggle: function() {
             if (this.isFolder) {
                 this.isOpen = !this.isOpen;
             }
         },
         makeFolder: function() {
             if (!this.isFolder) {
                 this.$emit("make-folder", this.item);
                 this.isOpen = true;
             }
         }
     }
 }
</script>
