<script setup>
import { ref,provide,watch,onMounted } from 'vue'
import axios from 'axios'
import PaneRight from '@/components/Panel/PaneRight.vue'
import { cityProvide, API_ENDPOINT } from '@/constans.js'
import PaneLeft from '@/components/Panel/PaneLeft.vue'

let data = ref()
// тут лежит статус 400
const errorMessage = ref()

const activeIndex = ref(0)

const city = ref('Москва')
provide(cityProvide, city)


watch(city, () => {
  getSity(city.value)
})

onMounted(() => {
  getSity(city.value)
})
async function getSity(city) {
  try {
    const params = new URLSearchParams({
      q: city,
      lang: 'ru',
      key: '3cd843a1001e4071b6d21048261001',
      aqi: 'yes',
      days: '4'
    })
    const res = await axios.get(API_ENDPOINT + '/forecast.json?' + params.toString())
    data.value = res.data
    errorMessage.value = null
  } catch (error) {
    data.value = null
    errorMessage.value = error.status // уходит стаутс 400
  }
}

</script>
<template>
  <main class="main">
    <div class="left">
      <PaneLeft v-if="data"
        :dayData="data.forecast.forecastday[activeIndex]"
        :activeIndex="activeIndex"
      />
    </div>
    <div class="right">
      <PaneRight
        :data="data"
        :errorMessage="errorMessage"
        :active-index="activeIndex"
        @select-index="(i) => activeIndex = i"
      />
    </div>
  </main>
</template>

<style scoped>
.main{
  display: flex;
  justify-content: center;
  align-items: center;
}
.right {
  background: var(--color-bg-main);
  padding: 44px 70px;
  border-radius: 25px 25px 25px 25px;
}

.left {
  width: 500px;
  height: 660px;
  border-radius: 30px;
  background-image: url("/public/bg.png");
  background-repeat: no-repeat;
  background-size: cover;
}
</style>

