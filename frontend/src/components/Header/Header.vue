зщ<template>
  <header>
    <div class="header__wrapper container">
      <nav class="header__nav">
        <ul class="header__nav-list">
          <li>
            <router-link to="/">Главная</router-link>
          </li>

          <li>
            <router-link to="/register">Регистрация</router-link>
          </li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script setup>

</script>

<style scoped>
.header__nav-list {
  display: flex;
  align-items: center;
  gap: 40px;
  color: #2c3e50;
}
</style>
