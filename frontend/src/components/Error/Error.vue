<script setup>
const { message } = defineProps({
  message: String
})
</script>

<template>
  <div class="error" v-if="message">
    {{ message }}
  </div>
</template>

<style scoped>
.error {
  font-size: 18px;
  padding: 15px;
  text-align: center;
  background: var(--color-bg-card);
  border-radius: 0px 0px 25px 25px;
  margin-top: -44px;
  margin-bottom: 60px;
}
</style>
