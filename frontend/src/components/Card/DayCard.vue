<script setup>
import IconSun from '@/icons/weather/IconSun.vue'
import IconRain from '@/icons/weather/IconRain.vue'
import IconCloud from '@/icons/weather/IconCloud.vue'

const { weatherCode, temp, date } = defineProps({
  weatherCode: Number,
  temp: Number,
  date: Date
})
</script>

<template>
  <button class="day-card">
    <IconSun v-if="weatherCode === 1000" size="55" color="red" />
    <IconRain v-if="weatherCode === 1009" size="55" color="red" />
    <IconCloud v-if="weatherCode === 2003" size="55" color="red" />

    <span class="day-card__day">
      {{ date.toLocaleDateString('ru-RU', {weekday: 'short'}) }}
    </span>
    <span class="day-card__temp">
      {{ temp }} °C
    </span>
  </button>

</template>

<style scoped>
.day-card {
  border-radius: 10px;
  box-shadow: 1px 2px 4px 0px #222831;
  color: var(--color-primary);
  padding: 20px 24px;
  background-color: var(--color-bg-card);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 15px;
  border: none;
  cursor: pointer;
}
.day-card:hover{
  background-color: #3a434f;
}
.day-card__day {
  font-size: 20px;
}
.day-card__temp {
  font-size: 20px;
  font-weight: 700;
}
</style>
