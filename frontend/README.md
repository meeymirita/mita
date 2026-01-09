Заметки функций из уроков

### `1- v-html`

**Назначение:** Директива для вставки HTML-кода в элемент. \
https://vuejs.org/api/built-in-directives.html#v-html

```vue

<script setup>
  const rawHtml = '<span style="color: red;">Красный текст</span>'
</script>

<template>
  <div v-html="rawHtml"></div>
</template>

```

### `2 - Биндинг стилей и классов`

https://vuejs.org/guide/essentials/class-and-style.html#class-and-style-bindings

```vue

<script setup>
  const textColor = 'red'
  const fontSize = 20
</script>
<template>
  <div :style="{ color: textColor, fontSize: fontSize + 'px' }">
    Текст с динамическими стилями
  </div>
</template>
```

### `3 - Тернарный оператор (true/false в шаблоне)`

#### Комплексный пример

```vue

<script setup>
  const colorRed = 'red'
  const user = {
    admin: {
      value: true,
      font: 25,
    },
  }
  const isAdmin = user.admin.value
</script>

<template>
  <main
    :style="{
      color: colorRed,
      fontSize: user.admin.font + 'px'
    }"
    :class="`color-${colorRed}`"
  >
    {{ isAdmin ? 'Администратор' : 'Обычный пользователь' }}
  </main>
</template>
```

### `4 - Slot`

https://vuejs.org/guide/components/slots.html#slots

Компонент Button.vue:

```vue

<template>
  <button class="button">
    <slot />
  </button>
</template>
```

Использование компонента:

```vue

<script setup>
  import Button from './Button.vue'
</script>

<template>
  <Button>Сохранить изменения</Button>
</template>
```

Может быть не ограниченное количество слотов  \
Слот по умолчанию всегда без имени, а дополнительные надо называть
``<slot name="plus"/>``
А также можно передать переменную ``currentDate``

```vue

<script setup>
  import Button from '@/components/Button/Button.vue'

  const currentDate = new Date().toLocaleDateString()
</script>

<template>
  <button class="button">
    <span>
      <slot name="currentDate" />
      <slot name="plus" />
      <slot name="minus" />
    </span>
    <slot />
  </button>
</template>
```

Использование в компоненте

Нужно указать тег `template` с именем слота `#plus`

```vue 

<template #plus /> 
```  

Пример c двумя доп слотами под плюс и минус:

```vue

<Button>
  <template #currentDate>{{currentDate}}</template>
  <template #plus>+</template>
  <template #minus>-</template>
  Сохранить
</Button>
```

### `5 - defineProps`

**Назначение:** чтобы объявить свойства, которые компонент принимает.

```vue
const {label = "Не задан", stat} = defineProps({
label: String, Принимаем label и stat
stat: String
})
Если при вызове компонента label не задан будет строка label = "Не задан"
```

**Деструктуризация пропсов:** В Vue 3.5 появилась возможность деструктурировать пропсы для упрощения их использования.

**Типизация пропсов:** Вместо массива можно использовать объект для явного указания типов свойств (например, string).
Это помогает избежать ошибок и не требует TypeScript.

**Указание значений по умолчанию:** Можно задать дефолтные значения для пропсов на случай, если они не переданы.

Компоент:
```Stat.vue```

```vue

<script setup>
  const { label = "Не задан", stat } = defineProps({
    label: String,
    stat: String
  })
</script>

<template>
  <div class="stat">
    <div class="stat-name">{{label}}</div>
    <div class="stat-value">{{stat}}</div>
  </div>
</template>
```

Компоент:
```App.vue```

```vue

<template>
  <main class="main">
    <Stat label="Влажность" stat="90%" />
    <Stat label="Осадки" stat="0%" />
    <Button>Сохранить</Button>
  </main>
</template>
```

Также в компоненте можно биндить по разному :

```vue
const data = {
label: "Влажность",
stat: "90%",
};
<Stat :label="data.label" :stat="data.stat" />
Тут уже сам подставит из даты
<Stat v-bind="data" />
```

### `6 - defineEmits и Валидация emits `

**Назначение:** объявляет события, которые компонент может отправлять наружу. \
**Концепции emit:** Emit означает отправлять или поднимать наверх события и данные из компонента.

#### defineProps vs defineEmits:

**defineProps:** позволяет компоненту принимать параметры. \
**defineEmits:** объявляет события, которые компонент может отправлять наружу.

```CitySelect.vue```

```vue

<script setup>
  "У меня есть событие 'selectCity"
  const emit = defineEmits(['selectCity'])
  "клик на кнопке"

  function select() {
    "выполнилось selectCity;  данные London "
    emit('selectCity', 'London')
  }
</script>

<template>
  <Button @click="select()">
    Изменить город
  </Button>
</template>
```

```App.vue```

```vue

<script setup>
  // функция обработчик
  function getCity(city) {
    console.log(city) // Получаем London
    Действия
    с
    данными
  }
</script>

<template>
  Слушаем событие от дочернего компонента
  <CitySelect @select-city="getCity" />
</template>
```

### `7 - $attrs`

**Практическое применение:**
Через зарезервированный $attrs можно получать и использовать атрибуты, автоматически примененные к компоненту. \

```App.vue```

```vue

<CitySelect class="asd" style="align-items: center" @select-city="getSity" />
```

```CitySelect.vue```

```vue

<Button @click="select()">
  <IconLocation />
  {{$attrs}}
  Изменить город

</Button>
{ "class": "asd", "style": { "align-items": "center" } } Изменить город
```

### `8 - Proxy`

```vue
const user = {
name: 'mirita'
}
const handler = {
get(target, prop, receiver) {
console.log("get value")
return target[prop]
},
set(obj, prop,value) {
if (prop == "name"){
console.log("write value")
obj[prop] = value;
return true
}
},
}
const proxy = new Proxy(user,handler)

console.log(proxy.name)
```

### `9 - ref и reactive`

**reactive:**

- Предназначен для объектов: массивов, объектов, Map и т.д.
  Не поддерживает примитивные типы (строки, числа, boolean).
  Преимущество: позволяет напрямую работать со свойствами объекта без обращения через .value.

**Недостатки:**

- Нельзя деструктурировать реактивные объекты. let { stat } = reactive([])
- Нельзя полностью заменять весь объект.

**ref:**

- Рекомендуется для всех типов данных (как примитивные, так и сложные).
- Для доступа к данным требуется использовать .value.
- Обеспечивает консистентность в приложении.

**Рекомендации:**
Использовать ref как основное средство работы с состоянием, следуя документации Vue.
ref подходит для работы как с примитивными, так и с более сложными типами данных.

ref

```vue 
let savedCity = ref('Moscow')
let data = ref({
label: "Влажность",
stat: "90%",
})
let arr = ref([1])
let map = ref(new Map([["1", 1]]))
function getSity(city){
savedCity.value = city
data.value.stat = '20%'
arr.value.push(2)
map.value.set("2",2)
}
</script>

<template>
  <main class="main">
    {{savedCity}}
    {{arr}}
    {{map}}
    <Stat v-bind="data" />
    <Stat label="Осадки" stat="0%" />
    <CitySelect @select-city="getSity" />
  </main>
</template>
```

reactive

```vue
let data = reactive({
label: "Влажность",
stat: "90%",
})
function getSity(city){
savedCity.value = city
data.stat = '20%'
}
</script>
```

### `10 - nextTick`

**nextTick:** возвращает Promise.

**Изменение состояния:**

- Когда вы изменяете реактивное состояние изменения, кажется, происходят мгновенно.
- Однако обновление шаблона (DOM) требует времени, и это важно учитывать.

**Вывод в консоль:**

- Если вы попытаетесь моментально вывести в консоль значение через \
  document.querySelector и innerHTML, вы можете увидеть старое значение, \
  несмотря на визуальное обновление.

```vue
async function getSity(city){
console.log(document.querySelector('#city').innerHTML) старое значение
await nextTick()
console.log(document.querySelector('#city').innerHTML) новое значение
}
```

- Использование await nextTick(), чтобы дождаться обновления DOM.
- Это гарантирует, что дальнейшие операции выполняются с уже обновленным DOM.

### `11 - computed`

**computed:** Это свойства, которые рассчитываются на основе других реактивных данных.

- Вычисляемые свойства автоматически кэшируются, и пересчитываются только если изменяются их зависимости.
- Повышение производительности за счет кэширования.
- Более чистый код, так как логика вывода данных выносится из шаблонов в скриптовую часть.

**Практическое применение:**

- Создаем реактивные состояния с помощью ref. data
- Используем computed для построения вычисляемых свойств. dataModified

```vue

<script setup>
  let savedCity = ref('Moscow')
  let data = ref({
    humidity: 90,
  })
  const dataModified = computed((prev) => {
    console.log(prev)
    return {
      label: "Влажность",
      stat: data.value.humidity + '%'
    }
  })
</script>

<template>
  <main class="main">
    <Stat v-bind="dataModified" />
  </main>
</template>

</style>
```

### `11 - v-show` отображение элементов

**Основные отличия:**

- v-show: Управляет отображением элемента через CSS-свойства, добавляя или убирая display: none. Элемент остаётся в DOM,
  даже если скрыт.
- v-if: Удаляет или добавляет элемент в DOM в зависимости от условия.
-

**Поддержка условий:**

- v-show: Не поддерживает v-else, v-else-if.
- v-if: Поддерживает использование дополнительных условий, таких как v-else, v-else-if.

**Производительность:**

- v-show: Предпочтителен для элементов, часто переключающихся между состояниями видимости, поскольку не требует
  повторного рендеринга.
- v-if: Подходит для редко изменяющихся элементов, чтобы уменьшить размер DOM.

**Пример использования:**

- Если нужно часто переключать отображение, как в случае с табами, лучше использовать v-show.
- Если изменения редки и важны условия, предпочтителен v-if.

### `11 - v-for` директива Vue.js для вывода списков.

**Примечание**
- Приоритет директив: v-if проверяется перед v-for.

**Ключи в списках** 
- **Зачем нужны ключи:** для отслеживания изменений (добавление/удаление элементов).

**Использование ключей:**
- Формат: :key="item".
- Преимущества: оптимизация производительности, корректная работа с изменениями.
- Рекомендация: всегда использовать уникальные идентификаторы (например, ID).

```vue
const arr = ref(
  ['Mira', 'Mirita', 'Mita']
)
<ul>
  <li v-for="(item, index) in arr" :key="item">
    {{index }}: {{ item}}
  </li>
</ul>
```
```vue
const obj = ref({
    name:"Mira",
    age:22
})

<ul>
  <li v-for="(value, key, index) in obj" :key="key">
    {{index }}: {{ value}} {{key}}
  </li>
</ul>
```
































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































