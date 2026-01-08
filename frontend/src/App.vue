<script setup>
import Button from '@/components/Button/Button.vue'
import Stat from '@/components/Stat.vue'
import IconLocation from '@/icons/IconLocation.vue'
import CitySelect from '@/components/Button/CitySelect.vue'

const data = {
  label: "Влажность",
  stat: "90%",
};

function getSity(city){
  console.log(city)
}
</script>

<template>
  <main class="main">
    <Stat :label="data.label" :stat="data.stat" />
    <Stat v-bind="data" />
    <Stat label="Осадки" stat="0%" />
    <CitySelect @select-city="getSity"/>
  </main>
</template>

<style scoped>
.main{
  background: var(--color-bg-main);
  padding: 60px 50px;
  border-radius: 25px;
}
</style>

