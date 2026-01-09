<script setup>

</script>

<template>
  <input class="input">
</template>

<style scoped>
.input{
  color: var(--color-primary);
  width: 260px;
  height: 53px;
  border: none;
  font-size: 18px;
  border-radius: 10px;
  padding: 15px 15px;
  box-shadow: 1px 2px 4px 0px var(--color-bg-main);
  background: var(--color-bg-card);
}
.input::placeholder{
  color: var(--color-text-placeholder) ;
  padding: 10px 10px;
}
</style>
