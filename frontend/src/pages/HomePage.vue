<script setup>
import { useUserStore } from '@/stores/user.js'

const userStore = useUserStore()

let user = {
  avatar: 'http://localhost:8080/storage/32/avatar.webp',
  created_at: '2026-01-05 21:15:54',
  email: 'mirita@mail.com',
  email_verified_at: null,
  id: 2,
  login: 'mira',
  name: 'mirita',
  status: 'pending',
  type: 'superadmin',
  updated_at: '2026-01-07 00:28:43',
};

userStore.setUser(user)
</script>

<template>
{{userStore.user}}
  <div class="row">
    Реализованный функционал
    <br><br>
    Регистрация <br> с подтверждением почты по коду <br>
    Повторная отправка кода на почту
    <br><br>
    Логин пользователя <br>
    Выход <br>
    Вывод постов <br>
    Профиль с загрузкой аватара и отправкой кода подтверждения на почту если емаил еще не
    подтверждён

  </div>
</template>
