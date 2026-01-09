<script setup>
import { ref, reactive, nextTick, computed } from 'vue'
import Stat from '@/components/Stat.vue'
import CitySelect from '@/components/Button/CitySelect.vue'

let savedCity = ref('Moscow')
let data = ref({
  humidity: 90,
  rain: 0,
  wind: 3
})
const dataModified = computed((prev) => {
  console.log(prev)
  return [
    {
      label: 'Влажность',
      stat: addPersentToStat(data.value.humidity, 'humidity')
    },
    {
      label: 'Осадки',
      stat: addPersentToStat(data.value.rain, 'rain')
    },
    {
      label: 'Ветер',
      stat: addPersentToStat(data.value.wind, 'wind')
    }

  ]
})
const addPersentToStat = (value,key) => {
  if (key === 'humidity' || key === 'rain'){
    return value + ' %'
  }
  return value + 'м/ч'
}

async function getSity(city) {
  savedCity.value = city
  data.value.humidity = 20
}
</script>
<template>
  <main class="main">
    <ul>
      <li v-for="(value, key, index) in dataModified">
        <Stat :label="value.label" :stat="value.stat" />
      </li>
    </ul>
    <div id="city">{{ savedCity }}</div>
    <!--    <Stat v-bind="dataModified" />-->
    <Stat label="Осадки" stat="0%" />
    <CitySelect @select-city="getSity" />
  </main>
</template>

<style scoped>
.main {
  background: var(--color-bg-main);
  padding: 60px 50px;
  border-radius: 25px;
}
</style>

