<script setup>
import { ref, reactive, nextTick, computed } from 'vue'
import axios from 'axios'
import Stat from '@/components/Stat/Stat.vue'
import CitySelect from '@/components/Button/CitySelect.vue'
import Error from '@/components/Error/Error.vue'
import DayCard from '@/components/Card/DayCard.vue'

const API_ENDPOINT = 'http://api.weatherapi.com/v1'

let data = ref()
const errorMap = new Map(
  [
    [
      400, 'Указанный город не найден'
    ]
  ]
)
// тут лежит статус 400
const errorMessage = ref();

const errorDisplay = computed(() => {
  // из мапы берем элемент по errorMessage.value который 400 при ошибке
  return errorMap.get(errorMessage.value)
})
const dataModified = computed((prev) => {
  if (!data.value) return []
  return [
    {
      label: 'Влажность',
      stat: data.value.current.humidity + " %"
    },
    {
      label: 'Облачность',
      stat: data.value.current.cloud + " %"
    },
    {
      label: 'Ветер',
      stat: data.value.current.wind_kph + " км/ч"
    }

  ]
})
async function getSity(city) {
  try{
    const params = new URLSearchParams({
      q: city,
      lang: 'ru',
      key: '3cd843a1001e4071b6d21048261001',
      aqi: 'yes',
      days: '3',
    })
    const res = await axios.get(API_ENDPOINT + '/forecast.json?' + params.toString())
    data.value = res.data
    console.log(data.value.forecast.forecastday)
    errorMessage.value = null
  } catch (error){
    data.value = null
    errorMessage.value = error.status // уходит стаутс 400
  }
}
</script>
<template>
  <main class="main">

    <DayCard :weather-code="1000" :temp="30" :date="new Date()" />
    <error :message="errorDisplay" />
    <Stat v-for="item in dataModified" v-bind="item" :key="item.label" />
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

