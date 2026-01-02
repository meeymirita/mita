<script setup>
import { ref, computed } from 'vue'
const currentPath = ref(window.location.hash)

console.log( currentPath.value);
</script>
<template>
  <div class="d-flex">
    <aside class="sidebar bg-light vh-100" style="width: 250px;">
      <div class="p-3">
        <h5 class="mb-4">Меню</h5>
        <ul class="nav flex-column">
          <li class="nav-item">
            <router-link to="/" class="nav-link">Главная</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/me" class="nav-link ">Профиль</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/register" class="nav-link">Регистрация</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/login" class="nav-link">Вход</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/logout" class="nav-link ">Выход</router-link>
          </li>
        </ul>
      </div>
    </aside>

    <main class="flex-grow-1 p-4">
      <RouterView/>
    </main>
  </div>
</template>
<style scoped>
.sidebar {
  border-right: 1px solid #dee2e6;
  min-height: 100vh;
}
.nav-link {
  color: #495057;
  padding: 0.5rem 0;
}
.nav-link:hover,
.nav-link.router-link-active {
  color: #0d6efd;
  font-weight: 500;
}
</style>
