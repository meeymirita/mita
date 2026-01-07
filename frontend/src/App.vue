<script setup>
const colorRed = 'red'

const user = {
  "admin": {
    "value": true,
    "font": 25,
  },
}
const isAdmin = user.admin.value
</script>

<template>
  <header>header</header>
  <main
    :style="{color:colorRed, fontSize: user.admin.font + 'px'}"
    :class="`color-${colorRed}`"
  >
    {{isAdmin ? "adm" : "no" }}
  </main>
  <footer>{{10 + 20}}</footer>
</template>

<style scoped></style>

