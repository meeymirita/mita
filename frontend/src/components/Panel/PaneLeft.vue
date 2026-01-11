<script setup>
import { computed, inject } from 'vue'
import IconSun from '@/icons/weather/IconSun.vue'
import { cityProvide } from '@/constans.js'
import IconLocation from '@/icons/IconLocation.vue'
import IconRain from '@/icons/weather/IconRain.vue'
import IconCloud from '@/icons/weather/IconCloud.vue'

const city = inject(cityProvide)

const { activeIndex, dayData } = defineProps({
  dayData: Object,
  activeIndex: Number
})

const day = computed((prev) => {
  return new Date(dayData.date).toLocaleDateString('ru-RU', {
    weekday: 'long'
  })
})
const dayFull = computed((prev) => {
  return new Date(dayData.date).toLocaleDateString('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
})

const code = computed(() => {
  return dayData.day.condition.code
})
</script>

<template>
  <div class="paneleft">
    <div>
      <div class="day">
        {{ day }}
      </div>
      <div class="dayFull">
        {{ dayFull }}
      </div>
      <div class="city">
        <IconLocation />
        {{ city }}
      </div>
    </div>
    <div>
      <div class="weatherIcon">
        <IconSun v-if="code <= 1003" :size="95" />
        <IconRain v-if="code >= 1006 && weatherCode < 1063" :size="95" />
        <IconCloud v-if="code >= 1063" :size="95" />
      </div>
      <div class="temp">
        {{dayData.day.avgtemp_c}}
      </div>
      <div class="condition">
        {{dayData.day.condition.text}}
      </div>
    </div>

    <!--    <div class="card-bottom">-->
    <!--      <IconSun :size="100"/>-->
    <!--      <span>29 °C</span>-->
    <!--      <span>Солнечно</span>-->
    <!--    </div>-->
  </div>


</template>

<style scoped>
.paneleft {
  display: flex;
  flex-direction: column;
  padding: 48px 32px;
  justify-content: space-between;
  height: 100%;
}

.day {
  color: var(--color-primary);
  font-weight: 700;
  font-size: 37px;
  text-transform: capitalize;
  margin-bottom: 16px;
}

.dayFull {
  font-size: 22px;
  font-weight: 500;
  margin-bottom: 16px;
}

.city {
  display: flex;
  align-items: center;
  gap: 8px;
}
.weatherIcon{
  margin: 25px;
}
.temp{
  font-size: 50px;
  font-weight: 700;
  margin-bottom: 9px;
}
.condition{
  font-size: 30px;
  font-weight: 700;
}
</style>
