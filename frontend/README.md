Заметки функций из уроков
- хороший гайд по axios https://purpleschool.ru/knowledge-base/article/axios
<a href="https://vuejs.org/guide/essentials/computed.html#computed-properties">Computed Properties</a>

### 1 - <a href="https://vuejs.org/api/built-in-directives.html#v-html">v-html</a> Директива для вставки HTML-кода в элемент.
```vue
<script setup>
  const rawHtml = '<span style="color: red;">Красный текст</span>'
</script>
<template>
  <div v-html="rawHtml"></div>
</template>
```
### 2 - <a href="https://vuejs.org/guide/essentials/class-and-style.html#class-and-style-bindings">Биндинг стилей и классов</a>
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
### 4 - <a href="https://vuejs.org/guide/components/slots.html#slots">Slot</a>

**Компонент Button.vue:**
```vue
<template>
  <button class="button">
    <slot />
  </button>
</template>
```
**Использование компонента:**
```vue
<script setup>
  import Button from './Button.vue'
</script>
<template>
  Текст внутри тега <Button> помещается в <slot/>
  <Button>Сохранить изменения</Button>
</template>
```
**Может быть не ограниченное количество слотов** 

**Слот по умолчанию всегда без имени, а дополнительные надо называть**
``<slot name="plus"/>``
А также можно передать переменную ``currentDate``
```vue
<script setup>
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
### 5 - <a href="https://vuejs.org/guide/components/props.html">defineProps / Props</a>

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
    Действия с данными
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
Получаем что передали в  (<CitySelect class="asd" style="align-items: center")
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
- Не поддерживает примитивные типы (строки, числа, boolean).
- Преимущество: позволяет напрямую работать со свойствами объекта без обращения через .value.

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

**ref**

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
**reactive**

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
### 11 - <a href="https://vuejs.org/guide/essentials/computed.html#computed-properties">Computed Properties</a> 

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
### `12 - v-show` отображение элементов

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
### `13 - v-for` директива Vue.js для вывода списков.

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
### `14 - v-model и defineModel `  автоматически связывает данные между моделью и компонентом.

**Как это работает?**
Убираем пропсы и эмиттеры, заменяя их на v-model.
Задаем модель как реактивное состояние, связываем ее с input через v-model.
**Практическая реализация:**

- Модель данных изменяется при обновлении input и наоборот.
- На вход компонента передаем модель через v-model, привязывая ее к значению input.
- Используем event.target.value для обновления модели при изменении input.

**Использование в браузере:**

- Обновляем страницу, изменения отражаются в input как и раньше.
- Подходит для разных элементов: input, textarea, checkbox, select и др.

**Автоматическое связывание:**

- v-model связывает поведение для стандартных input-компонентов.
- Значения автоматически сопоставляются: для checkbox это checked.

**Преимущества:**

- Упрощение кода за счет устранения необходимости в явных обработчиках событий.
- Работает из коробки с различными типами input-компонентов.


**defineModel и типизация:**

- ESLint требует задать тип для вашей модели.
- Определяем модель как строку и переименовываем её в "дата" для удобства.
- Модель становится реактивным значением без изменений в поведении.

**Обязательные параметры (required):**

- Используем параметр required для обозначения обязательности модели.
- При отсутствии модели в консоли появляется предупреждение "Missing Required Props Model Value".
- required: true гарантирует, что модель обязательно должна быть.

**Дефолтные значения:**

- Можем задать дефолтное значение модели, если его нет.
- В текущем случае не применимо из-за передачи значений из реактивного состояния.

**Модели с наименованием:**

- Возможность задавать модели с уникальным именем, например, "additional".
- Такой подход позволяет иметь несколько моделей в одном компоненте.
- Используем синтаксис B-model:имя для обращения к конкретной модели.



```CitySelect.vue```

```vue
const city = ref('Moscow')
  <input-search
     placeholder="Введите город"
     v-model="city"
/>
```

```InputSearch.vue```

```vue
const data = defineModel({
type: String, required: true, default: "default"
})

<input
   v-model="data"
   class="input"
/>

```
### 15 - <a href="https://ru.vuejs.org/guide/essentials/lifecycle">Lifecycle Hooks</a> Жизненный цикл компонентов в Vue

Жизненный цикл компонента:
1)  **setup:**
- Инициализация компонента через setup-функцию.
- Возможность использования хука beforeCreate, чтобы выполнить действия до создания компонента.

2) **Создание:**

- Хук created вызывается после инициализации компонента.
- Если шаблон компонента не прикомпилирован, происходит его компиляция.

3) **Первичный рендеринг:**

- Компилированный шаблон отображается в браузере, создаются и вставляются нужные ноды в DOM.
- Возможность использования хуков beforeMount и mounted для выполнения действий перед монтированием компонента и после него.

4) **Обновление:**

- Любые изменения в реактивных состояниях могут вызвать ререндер компонента.
- Хуки beforeUpdate и updated позволяют выполнять действия до и после обновления компонента.

4) **Размонтирование:**

- Когда компонент больше не нужен, он становится unmounted.
- Хуки beforeUnmount и unmounted позволяют выполнять действия перед размонтированием и после него (например, очистка таймеров или интервалов).
- Знание этапов жизненного цикла и использование хуков позволяет эффективно управлять компонентами и их состоянием в приложении. Теперь мы перейдем к практической части, чтобы на практике применить теоретические знания и использовать хуки в реальном проекте.

**onMounted:** Выполняется после полной отрисовки компонента. Обычно используется чаще всего для работы с компонентом после его первого рендера.
```vue
onMounted(() => {
console.log('city select mounted')
})
```

**onBeforeMount:** Выполняется перед началом монтирования.
```vue
onBeforeMount(() => {
console.log('city select before mounted')
})
```
**Хуки обновления:**
- onUpdated: Используется при каждом обновлении компонента, например, когда меняется его состояние.
- onBeforeUpdate: Позволяет выполнить код перед обновлением компонента.

**Хуки размонтирования:**
- onUnmount: Активируется, когда компонент удаляется из DOM. Компонент не размонтируется при использовании v-show, а просто скрывается. v-if, в отличие от v-show, позволяет полное удаление компонента, обеспечивая выполнение onUnmount.
### 16 - <a href="https://ru.vuejs.org/guide/essentials/lifecycle">watch</a> позволяет отслеживать изменения конкретного источника 

**Цикл обновления и ограниченность onUpdatedHook:**
- Использование onUpdatedHook может показаться удобным для отслеживания изменений, но он реагирует на любые изменения в компоненте, а не только на целевое состояние (например, город).

**Функция Watch:**
- Watch позволяет отслеживать изменения конкретного источника (например, города) и выполнять callback-функцию при изменении.
- Callback принимает два аргумента: новая и старая значение, что позволяет учитывать изменения.

**Параметры Watch:**
- immediate: вызывает Watch сразу при инициализации компонента.
- once: выполнять Watch только один раз после первого изменения.

```vue
watch(
  // принимаем что будем просматривать
  city,
  // колбек принимает старое и новое значение
  (newValue, oldValue) => {
  console.log(`city new: ${newValue}` )
  console.log(`city old: ${oldValue}` )
},
  //вызывает Watch сразу при инициализации компонента.
  { immediate: true}
)
```

**Функция watchEffect:**
- watchEffect позволяет отслеживать изменения нескольких значений.
- Поддерживает автоматическое отслеживание реактивного состояния без явного указания зависимостей.

```vue
watchEffect(()=> {
console.log(city.value)
console.log(isEdited.value)
})
```


**Очистка эффектов:**
- Возможность очистки предыдущих эффектов (например, отмена запросов), чтобы предотвратить нежелательные действия.

```vue
onWatcherCleanup(() => {
    console.log('onWatcherCleanup')
  })
```

**Отмена Watch:**
- Watch возвращает функцию, позволяющую отменить актуальность отслеживания при необходимости.
- Рекомендация: минимизировать использование отмены, если watch и watchEffect находятся на верхнем уровне.


### 17 - Создание своиx директив

**Что такое директива:**
Это объект, позволяющий привязываться к жизненным циклам элемента, на который она применяется.


**Привязывание к событиям:**
Используем событие ```mounted``` для выполнения фокусировки элемента после его монтирования.

**Регистрация директивы:**

**Локально:** определить директиву прямо в компоненте. Начинаются с ```v```
```vue
const vFocus = {
  mounted: (el) => el.focus(),
}
Вызов на компоненте v-focus
<input-search v-focus />
```
**Глобально:** зарегистрировать директиву в main.js, чтобы использовать во всем приложении. Используйте app.directive('focus', {...}).

```vue
import './assets/main.css'
import { createApp } from 'vue'
import App from './App.vue'

const app = createApp(App)

Уже не в компоненте а глобально чтоб переиспользовать
app.directive('focus', {
  mounted: (el) => el.focus()
})

app.mount('#app')
```
### 18 - <a href="https://vuejs.org/guide/components/provide-inject.html">Provide / Inject</a> 
**Provide:** функция для предоставления данных на высокоуровневом компоненте, где данные генерируются.

**Inject:** функция для получения данных в низкоуровневом компоненте, которому данные нужны.
Позволяет избежать ненужной передачи данных через промежуточные компоненты.

На картинке из рут мы прокидываем в любой дочерний компонент

<img width="800px" src="https://vuejs.org/assets/provide-inject.C0gAIfVn.png">

**Пример:** ```App.vue```

Тут отправляем 1

```vue
provide('num', 1)
```

**Получаем:** ``` App/PaneRight/CitySelect.vue```
```vue
const num = inject('num')
console.log('из апп вью в компонент' + ' ' +num)
```

**Использование символов вместо строк для ключей**

- Символы всегда уникальны, даже если они имеют одинаковый дескриптор, что предотвращает коллизии.
- Для хранения символов создайте отдельный файл, например, constants.js.
- В constants.js экспортируются символы, например, export const CityProvide = Symbol('City');.
- Можно также вынести в отдельные файлы другие константы, такие как API endpoints.
- Импортируйте их в нужных местах для улучшения структуры кода.

**Пример выше уже на Symbol('City')**

**Пример:** ```App.vue```

Тут отправляем 

```vue
import { cityProvide } from '@/constans.js' => Вернёт = export const cityProvide = Symbol('city')

provide(cityProvide, city)
```

**Получаем:** ``` App/PaneRight/CitySelect.vue```
```vue
import { cityProvide } from '@/constans.js'

const city = inject(cityProvide)
console.log('из апп вью в компонент' + ' ' +num)
```

**Таким образом, символы обеспечивают уникальность и предотвращают возможные конфликты в проекте.**