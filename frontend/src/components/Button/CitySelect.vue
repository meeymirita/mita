<script setup>
import { ref, onMounted} from 'vue'
import Button from '@/components/Button/Button.vue'
import IconLocation from '@/icons/IconLocation.vue'
import InputSearch from '@/components/Input/InputSearch.vue'

const isEdited = ref(false)
const city = ref('Moscow')
const emit = defineEmits(['selectCity'])

onMounted(() => {
  emit('selectCity', city.value)
})

function select() {
  isEdited.value = false
  emit('selectCity', city.value)
}

function edit() {
  isEdited.value = true
}

</script>

<template>
  <div class="city-select">
    <div class="city-input" v-if="isEdited">
      <input-search
        @keyup.enter="select"
        placeholder="Введите город"
        v-model="city"
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
