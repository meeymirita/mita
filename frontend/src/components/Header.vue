<script setup>

</script>
<template>
  <header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li>
            <router-link to="/posts" class="nav-link px-2 link-secondary">Главная</router-link>
          </li>
          <li>
            <router-link to="/register" class="nav-link px-2 link-secondary">Регистрация</router-link>
          </li>
          <li>
            <router-link to="/login" class="nav-link px-2 link-secondary">Вход</router-link>
          </li>
          <li>
            <router-link to="/" class="nav-link px-2 link-secondary">Инфо</router-link>
          </li>
        </ul>
        <div class="dropdown text-end">
          <router-link to="/me" class="d-block link-body-emphasis text-decoration-none dropdown-toggle">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </router-link>

          <ul class="dropdown-menu text-small" style="">
            <li>
              <a class="dropdown-item" href="#">New project...</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>

</style>
