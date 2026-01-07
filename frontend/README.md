Заметки функций из уроков


v-html
v-html позволяет вставить html
https://vuejs.org/api/built-in-directives.html#v-html

2 - Биндинг стилей
https://vuejs.org/guide/essentials/class-and-style.html#class-and-style-bindings
const colorRed = 'red'
:style="{color:colorRed}"

3 - try/false в шаблоне
const isAdmin = true
<main>{{isAdmin ? 'adm' : 'no' }}</main>
еще больший пример из доки 
const colorRed = 'red'
const user = {
    "admin": {
        "value": true,
        "font": 25,
    },
}
const isAdmin = user.admin.value
<main
  :style="{color:colorRed, fontSize: user.admin.font + 'px'}"
  :class="`color-${colorRed}`"
>
  {{isAdmin ? 'adm' : 'no' }}
</main>