// сразу из примера https://pinia.vuejs.org/core-concepts/
// пример про счётчик
// export const useCounterStore = defineStore('counter', () => {
//   const count = ref(0)
//   const name = ref('Eduardo')
//   const doubleCount = computed(() => count.value * 2)
//   function increment() {
//     count.value++
//   }
//
//   return { count, name, doubleCount, increment }
// })
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),
  actions: {
    setUser(userData) {
      this.user = userData
    },
    getUser() {
      console.log('функция getUser')
    },
    unsetUser(){
      console.log('функция unsetUser')
    },
    updateUserFromUpdateData() {
      console.log('updateUserFromUpdateData')
    }
  }


})
