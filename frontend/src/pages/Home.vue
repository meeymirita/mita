<template>
  <div class="home__wrapper">
    <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125"/>

    <div class="wrapper">
      <HelloWorld msg="You did it!"/>
    </div>

    <button @click="showToast" class="test-btn">
      Test Toast Notification
    </button>
  </div>
</template>

<script setup>

import HelloWorld from "@/components/HelloWorld.vue";
import { useToast } from "vue-toastification";
const toast = useToast();

const showToast = () => {
  console.log('qwe');
  toast.success("reze", {
    position: "top-right",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
  });
};
</script>

<style  scoped>
.home__wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  height: 100%;
  gap: 50px;
}
</style>
