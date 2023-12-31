<script setup>
import { mdiForwardburger, mdiBackburger, mdiMenu } from "@mdi/js";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import menuAside from "@/menuAside.js";
import menuNavBar from "@/menuNavBar.js";
import { useMainStore } from "@/stores/main.js";
import { useStyleStore } from "@/stores/style.js";
import BaseIcon from "@/Components/Partials/BaseIcon.vue";
import FormControl from "@/Components/Partials/FormControl.vue";
import NotificationSuccess from "@/Components/Partials/NotificationSucces.vue";
import NotificationError from "@/Components/Partials/NotificationError.vue";
import NavBar from "@/Components/Partials/NavBar.vue";
import NavBarItemPlain from "@/Components/Partials/NavBarItemPlain.vue";
import AsideMenu from "@/Components/Partials/AsideMenu.vue";
import FooterBar from "@/Components/Partials/FooterBar.vue";
import { colorsBgLight, colorsOutline } from "@/colors";
router.on("navigate", () => {
    isAsideMobileExpanded.value = false;
    isAsideLgActive.value = false;
});
useMainStore().setUser({
  name: "John Doe",
  email: "john@example.com",
  avatar:
    "https://avatars.dicebear.com/api/avataaars/example.svg?options[top][]=shortHair&options[accessoriesChance]=93",
});

const layoutAsidePadding = "xl:pl-60";

const styleStore = useStyleStore();

 // const router = useRouter();

const isAsideMobileExpanded = ref(false);
const isAsideLgActive = ref(false);

// router.beforeEach(() => {
//   isAsideMobileExpanded.value = false;
//   isAsideLgActive.value = false;
// });

const menuClick = (event, item) => {
  if (item.isToggleLightDark) {
    styleStore.setDarkMode();
  }

  if (item.isLogout) {
      router.post(route("logout"));
  }
};

</script>

<template>
  <div
    :class="{
      dark: styleStore.darkMode,
      'overflow-hidden lg:overflow-visible': isAsideMobileExpanded,
    }"
  >

    <div
      :class="[layoutAsidePadding, { 'ml-60 lg:ml-0': isAsideMobileExpanded }]"
      class="pt-14 min-h-screen w-screen transition-position lg:w-auto bg-gray-50 dark:bg-slate-800 dark:text-slate-100"
    >
      <NavBar
        :menu="menuNavBar"
        :class="[
          layoutAsidePadding,
          { 'ml-60 lg:ml-0': isAsideMobileExpanded },
        ]"
        @menu-click="menuClick"
      >

        <NavBarItemPlain
          display="flex lg:hidden"
          @click.prevent="isAsideMobileExpanded = !isAsideMobileExpanded"
        >
          <BaseIcon
            :path="isAsideMobileExpanded ? mdiBackburger : mdiForwardburger"
            size="24"
          />
        </NavBarItemPlain>
        <NavBarItemPlain
          display="hidden lg:flex xl:hidden"
          @click.prevent="isAsideLgActive = true"
        >

          <BaseIcon :path="mdiMenu" size="24" />
        </NavBarItemPlain>

      </NavBar>
      <AsideMenu :roles="$page"
        :is-aside-mobile-expanded="isAsideMobileExpanded"
        :is-aside-lg-active="isAsideLgActive"
        :menu="menuAside"
        @menu-click="menuClick"
        @aside-lg-close-click="isAsideLgActive = false"
      />
        <slot name="Head" />
        <NotificationSuccess v-if="$page.props.flash.message" class="fixed right-5 text-white z-10" :class="colorsBgLight.success">
            <div  class="alert pr-3 max-w-sm">
                {{ $page.props.flash.message }}
            </div>
        </NotificationSuccess>
        <NotificationError v-if="$page.props.flash.error" class="fixed right-5 dark:bg-red-500  text-white z-10" :class="colorsBgLight.danger">
            <div  class="alert pr-3 max-w-sm">
                {{ $page.props.flash.error }}
            </div>
        </NotificationError>
      <slot />
      <FooterBar>
        Get more with
        <a
          href="https://tailwind-vue.justboil.me/"
          target="_blank"
          class="text-blue-600"
          >Premium version</a
        >
      </FooterBar>
    </div>
  </div>
</template>
