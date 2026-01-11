<script setup>
import { ref,inject} from 'vue'
import Button from '@/components/Button/Button.vue'
import IconLocation from '@/icons/IconLocation.vue'
import InputSearch from '@/components/Input/InputSearch.vue'
import { cityProvide } from '@/constans.js'

const city = inject(cityProvide)
const inputValue = ref(city.value)

const isEdited = ref(false)

function select() {
  isEdited.value = false
  city.value = inputValue.value

}

function edit() {
  isEdited.value = true
}

</script>

<template>
  <div class="city-select">
    <div class="city-input" v-if="isEdited">
      <input-search
        v-focus
        @keyup.enter="select"
        placeholder="Введите город"
        v-model="inputValue"
      />
      <Button @click="select()">
        Сохранить
      </Button>
    </div>
    <Button v-else @click="edit()">
      <IconLocation />
      Изменить город
    </Button>
  </div>
</template>
<style scoped>
.city-input {
  display: flex;
  gap: 15px;
}

.city-select {
  width: 420px;
}
</style>
