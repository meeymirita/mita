<template>
    <div class="row">
qwe
    </div>
</template>

<script setup>

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

