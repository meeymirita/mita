<script setup>
import {computed} from 'vue'
import Stat from '@/components/Stat/Stat.vue'
import DayCard from '@/components/Card/DayCard.vue'
import CitySelect from '@/components/Button/CitySelect.vue'
import Error from '@/components/Error/Error.vue'
import { errorMap } from '@/constans.js'


const { data, errorMessage,activeIndex } = defineProps({
  data: Object,
  errorMessage : Number,
  activeIndex: Number,
})

const emit = defineEmits(["select-index", "select-city"])

const errorDisplay = computed(() => {
  // из мапы берем элемент по errorMessage.value который 400 при ошибке
  return errorMap.get(errorMessage)
})

const statData = computed((prev) => {
  console.log(data.forecast.forecastday[activeIndex].day)
  if (!data) return []
  return [
    {
      label: 'Влажность',
      stat: data.forecast.forecastday[activeIndex].day.avghumidity + ' %'
    },
    {
      label: 'Вероятность дождя',
      stat: data.forecast.forecastday[activeIndex].day.daily_chance_of_rain + ' %'
    },
    {
      label: 'Ветер',
      stat: data.forecast.forecastday[activeIndex].day.maxwind_kph + ' км/ч'
    },
  ]
})
</script>

<template>
  <error v-if="errorDisplay" :message="errorDisplay" />
  <div v-if="data" class="stat-data">
    <div class="stat-list">
      <Stat v-for="item in statData" v-bind="item" :key="item.label" />
    </div>
    <div class="day-card-list">
      <DayCard
        v-for="(item, i) in data.forecast.forecastday"
        :key="item.date"
        :weather-code="item.day.condition.code"
        :temp="item.day.avgtemp_c"
        :date="new Date(item.date)"
        :is-active="activeIndex === i"
        @click="() => (emit('select-index', i))"
      />
    </div>
  </div>

  <CitySelect  />
</template>

<style scoped>

.day-card-list {
  display: flex;
  gap: 5px;
}

.stat-data {
  display: flex;
  flex-direction: column;
  gap: 80px;
  margin-bottom: 70px;
}

.stat-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 70px;
}
</style>
