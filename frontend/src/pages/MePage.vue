<script setup>
import { ref, onMounted, watch, onUnmounted,computed  } from 'vue'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notification.js'
import VuePictureCropper, { cropper } from 'vue-picture-cropper'
import apiClient from '@/api/axios.js'

const notification = useNotificationStore()

const router = useRouter()
const user = ref(null)
const localUser = ref({})
const loading = ref(true)
const error = ref('')

const fileInput = ref(null)
const showCropModal = ref(false)
const hasChanges = ref(false)
const saving = ref(false)
const imageSrc = ref('')
const croppedImage = ref('')

// Таймер
const sendCode = ref(false)
const timerCount = ref(0) // секунды
const timerInterval = ref(null)
const started = ref(false)

// watch за change user и fresh localUser
watch(user, (newUser) => {
  if (newUser) {
    localUser.value = { ...newUser }
  }
}, { immediate: true })

const fetchUserData = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await apiClient.get('/user/me')
    user.value = response.data.data
    console.log(user.value)
    localUser.value = { ...response.data.data }
    hasChanges.value = false

    notification.info('Данные загружены!')

  } catch (err) {
    notification.error('Произошла ошибка 😭😭😭😭')
    if (err.response?.status === 401) {
      error.value = 'Вы не авторизованы'
      setTimeout(() => router.push('/login'), 2000)
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Ошибка при загрузке данных'
    }
  } finally {
    loading.value = false
  }
}
// auth_token
onMounted(() => {
  const token = localStorage.getItem('auth_token')
  if (!token) {

    notification.unauthorized()

    router.push('/login')
    return
  }
  fetchUserData()
})
// logout
const logout = async () => {
  await apiClient.post('/user/logout')
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user_email')
  notification.logoutSuccess()
  router.push('/login')
}

// Выбор файла
const onFileChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  if (file.size > 5 * 1024 * 1024) {
    notification.error('Файл слишком большой. Максимальный размер: 5MB')
    return
  }
  if (!file.type.match('image.*')) {
    notification.error('Пожалуйста, выберите изображение')
    return
  }

  const reader = new FileReader()
  reader.onload = (event) => {
    imageSrc.value = event.target.result
    showCropModal.value = true
  }
  reader.readAsDataURL(file)
}
const cancelCrop = () => {
  showCropModal.value = false
  imageSrc.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
const crop = () => {
  if (!cropper) return

  const base64 = cropper.getDataURL({
    width: 500,
    height: 500
  })

  croppedImage.value = base64
  showCropModal.value = false
}
const rotateLeft = () => {
  if (cropper) cropper.rotate(-90)
}
const rotateRight = () => {
  if (cropper) cropper.rotate(90)
}
const resetCrop = () => {
  if (cropper) cropper.reset()
}

// Загрузка на сервер
const upload = async () => {
  const response = await fetch(croppedImage.value)
  const blob = await response.blob()

  const mimeType = blob.type || 'image/jpeg'
  const extension = mimeType.split('/')[1]

  const file = new File([blob], `avatar.${extension}`, { type: mimeType })

  const formData = new FormData()
  formData.append('avatars', file)

  try {
    await apiClient.post('/user/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      timeout: 30000
    })
    notification.avatarUpdated('Аватар успешно обновлен!')

    croppedImage.value = ''
    if (fileInput.value) fileInput.value.value = ''
    fetchUserData()
  } catch (error) {
    console.error('Ошибка:', error)
    notification.fileUploadError()
  }
}

// Форматирование даты
const formatDate = (dateString) => {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleAvatarError = (e) => {
  e.target.style.display = 'none'
}

// Взаимодействие с основной информацией
const updateField = (field, value) => {
  localUser.value[field] = value
  hasChanges.value = true
}
// не делал еще
const saveChanges = async () => {
  saving.value = true
  try {
    await apiClient.post('/user/update', localUser.value)
    await fetchUserData()
    notification.profileSaved('Изменения сохранены!')
  } catch (error) {
    notification.error('Ошибка сохранения')
    console.error('Ошибка сохранения:', error)
  } finally {
    saving.value = false
  }
}

const resetFields = () => {
  localUser.value = { ...user.value }
  hasChanges.value = false
  notification.warning('Изменения были отменены')
}

const sendVerificationEmail = async () => {
  try {

    if (!user.value?.email) {
      notification.error('Email не указан')
      return
    }

    const formData = new FormData()
    const email = user.value.email
    formData.append('email', email)

    await apiClient.post('/user/send-verification-code', formData)
    notification.emailSent()
    startTimer(1)
  } catch (error) {
    console.error('Ошибка отправки:', error)
    notification.error('Ошибка отправки')
  }
}
// Форматирование времени
const formattedTime = computed(() => {
  const minutes = Math.floor(timerCount.value / 60)
  const seconds = timerCount.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

onUnmounted(() => {
  stopTimer()
})
const stopTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
    timerInterval.value = null
  }
  started.value = false
  sendCode.value = false
  timerCount.value = 0
}
const startTimer = (minutes = 1) => {
  // Если таймер уже запущен — выходим
  if (started.value) return

  // Устанавливаем время в секундах
  timerCount.value = minutes * 60
  started.value = true
  sendCode.value = true

  // Запускаем таймер
  timerInterval.value = setInterval(() => {
    timerCount.value--

    // Если время вышло
    if (timerCount.value <= 0) {
      stopTimer()
      notification.info('Можно отправить код повторно')
    }
  }, 1000)
}

</script>

<template>
  <div class="user-profile">
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Загрузка данных...</p>
    </div>
    <div v-else-if="error" class="error">
      <div class="error-icon">!</div>
      <div class="error-content">
        <h3>Ошибка</h3>
        <p>{{ error }}</p>
        <button @click="fetchUserData" class="btn-retry">Повторить попытку</button>
      </div>
    </div>

    <div v-else-if="user" class="profile-container">
      <!-- Боковая панель с информацией -->
      <div class="sidebar">
        <div class="avatar-section">
          <div class="avatar-wrapper">
            <img
              :src="user.avatar || '/default-avatar.jpg'"
              :alt="user.name"
              class="user-avatar"
              @error="handleAvatarError"
            />
            <div v-if="!user.avatar" class="avatar-placeholder">
              {{ user.name }}
            </div>
          </div>
          <h2 class="user-name">{{ user.name }}</h2>
          <p class="user-email">{{ user.email }}</p>
        </div>

        <div class="stats-section">
          <div class="stat-item">
            <span class="stat-label">ID пользователя</span>
            <span class="stat-value">{{ user.id }}</span>
          </div>
          ТИПО САЙД БАР СУДА МОЖНО ПОСТЫ И ТД
        </div>

        <button @click="logout" class="logout-btn">
          <span class="logout-icon">↩</span>
          Выйти из аккаунта
        </button>
      </div>

      <div class="main-content">
        <div class="content-header">
          <h1>Настройки профиля 👉👈</h1>
        </div>

        <div class="avatar-upload-section">
          <div class="section-header">
            <div class="avatar-info">
              <h4>Текущий аватар</h4>
              <p>Рекомендуемый размер: 500×500 пикселей</p>
              <p>Форматы: jpeg, png, jpg, svg, webp </p>
              <p>Макс. размер: 5MB</p>
            </div>
          </div>

          <div class="upload-container">
            <div class="current-avatar">
              <div class="avatar-preview">
                <img
                  v-if="croppedImage"
                  :src="croppedImage"
                  alt="Новый аватар"
                  class="new-avatar"
                />
                <img
                  v-else
                  :src="user.avatar || '/default-avatar.jpg'"
                  :alt="user.name"
                  class="current-avatar-img"
                />
                <div v-if="!user.avatar && !croppedImage" class="avatar-placeholder-large">
                  {{ user.name}}
                </div>
              </div>
            </div>

            <div class="upload-controls">
              <label class="file-upload-btn">
                <input
                  type="file"
                  @change="onFileChange"
                  accept="image/*"
                  class="file-input-hidden"
                  ref="fileInput"
                />
                <span class="upload-icon">📁</span>
                <span>Выбрать файл</span>
              </label>

              <div class="upload-hint">
                Нажмите, чтобы выбрать изображение с вашего устройства
              </div>

              <div v-if="croppedImage" class="upload-actions">
                <button @click="upload" class="btn-primary">
                  <span class="btn-icon">✓</span>
                  Сохранить изменения
                </button>
                <button @click="croppedImage = ''" class="btn-secondary">
                  <span class="btn-icon">↶</span>
                  Отменить
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="info-section">
          <div class="section-header">
            <h3>Информация о профиле</h3>
            <p>Основные данные вашей учетной записи</p>
          </div>

          <div class="info-grid">

            <div class="info-card shadow-sm border-0">
              <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex align-items-center mb-3">
                  <div>
                    <h4 class="mb-0 fw-bold text-dark">Личная информация 💞</h4>
                    <p class="text-muted mb-0">Редактирование данных профиля 💞💞💞</p>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none"
                        id="nameInput"
                        placeholder="Ваше имя"
                        :value="user.name"
                        @input="updateField('name', $event.target.value)"
                      >
                      <label for="nameInput" class="text-muted">
                        <i class="bi bi-person me-2"></i>Имя
                      </label>
                      <div class="form-text">Ваше отображаемое имя</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none"
                        id="loginInput"
                        placeholder="Логин"
                        :value="user.login"
                        @input="updateField('login', $event.target.value)"
                      >
                      <label for="loginInput" class="text-muted">
                        <i class="bi bi-at me-2"></i>Логин
                      </label>
                      <div class="form-text">Уникальный</div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="email"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none"
                        id="emailInput"
                        placeholder="Email"
                        :value="user.email"
                        @input="updateField('email', $event.target.value)"
                      >
                      <label for="emailInput" class="text-muted">
                        <i class="bi bi-envelope me-2"></i>Email
                      </label>
                      <div class="form-text">
                      <span v-if="user.email_verified_at" class="text-success">
                        <i class="bi bi-check-circle-fill me-1"></i>Подтвержден
                      </span>
                        <span v-else class="text-warning">
                        <i class="bi bi-exclamation-circle me-1"></i>Не подтвержден
                      </span>
                      </div>
                    </div>
                  </div>

                  <!--disabled-->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select
                        class="form-select border-start-0 border-top-0 border-end-0 rounded-0 shadow-none"
                        id="typeSelect"
                        :value="user.type"
                        @change="updateField('type', $event.target.value)"
                        :disabled="true"
                      >
                        <option value="user">👤 Обычный пользователь</option>
                        <option value="admin">👑 Администратор</option>
                        <option value="superadmin">⭐ Няшка</option>
                      </select>
                      <label for="typeSelect" class="text-muted">
                        <i class="bi bi-person-badge me-2"></i>Тип аккаунта
                      </label>
                    </div>
                  </div>

                  <!--disabled-->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <select
                        class="form-select border-start-0 border-top-0 borderend-0 rounded-0 shadow-none"
                        id="statusSelect"
                        :value="user.status"
                        :disabled="true"
                      >
                        <option value="pending" class="text-secondary">⏳ В ожидании</option>
                        <option value="active" class="text-success">✅ Активен</option>
                        <option value="inactive" class="text-warning">⏸️ Не активен</option>
                        <option value="rejected" class="text-danger">🚫 Заблокирован</option>
                      </select>
                      <label for="statusSelect" class="text-muted">
                        <i class="bi bi-circle-fill me-2"></i>Статус
                      </label>
                    </div>
                  </div>

                  <!--disabled-->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none bg-light"
                        id="emailVerifiedInput"
                        :value="user.email_verified_at ? formatDate(user.email_verified_at) : 'Не подтвержден'"
                        readonly
                        disabled
                      >
                      <label for="emailVerifiedInput" class="text-muted">
                        <i class="bi bi-shield-check me-2"></i>Email подтвержден
                      </label>
                      <div class="form-text mt-2">
                        <div v-if="!user.email_verified_at">
                          <!-- Кнопка отправки -->
                          <button
                            class="btn btn-sm btn-outline-primary d-flex align-items-center"
                            @click="sendVerificationEmail"
                            :disabled="sendCode"
                          >
                            <i class="bi bi-send me-2"></i>
                            <span v-if="sendCode">
        <span class="spinner-border spinner-border-sm me-2"></span>
        Отправка...
      </span>
                            <span v-else>Отправить подтверждение</span>
                          </button>

                          <div v-if="started" class="mt-2">
                            <div class="d-flex align-items-center gap-2">
                              <div class="timer-badge bg-light text-dark rounded-pill px-3 py-1 d-flex align-items-center">
                                <i class="bi bi-clock-history me-2 text-primary"></i>
                                <span class="fw-medium">{{ formattedTime }}</span>
                              </div>
                              <span class="text-muted small">
          Повторная отправка через
        </span>
                            </div>

                            <div class="progress mt-2" style="height: 4px;">
                              <div
                                class="progress-bar bg-primary"
                                role="progressbar"
                                :style="{ width: `${(timerCount / 60) * 100}%` }"
                                aria-valuenow="timerCount"
                                aria-valuemin="0"
                                aria-valuemax="60"
                              ></div>
                            </div>
                          </div>

                          <div v-else class="text-muted small mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Нажмите, чтобы отправить код подтверждения на email
                          </div>
                        </div>

                        <div v-else class="alert alert-success alert-sm d-flex align-items-center mt-2 py-2">
                          <i class="bi bi-check-circle-fill me-2"></i>
                          <div>
                            <div class="fw-medium">Email подтвержден</div>
                            <small class="text-muted">Подтверждено: {{ formatDate(user.email_verified_at) }}</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!--disabled-->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none bg-light"
                        id="createdAtInput"
                        :value="formatDate(user.created_at)"
                        readonly
                        disabled
                      >
                      <label for="createdAtInput" class="text-muted">
                        <i class="bi bi-calendar-event me-2"></i>Дата регистрации
                      </label>
                    </div>
                  </div>

                  <!--disabled-->
                  <div class="col-md-6">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control border-start-0 border-top-0 border-end-0 rounded-0 shadow-none bg-light"
                        id="updatedAtInput"
                        :value="formatDate(user.updated_at)"
                        readonly
                        disabled
                      >
                      <label for="updatedAtInput" class="text-muted">
                        <i class="bi bi-clock-history me-2"></i>Последнее обновление
                      </label>
                    </div>
                  </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Изменения сохраняются автоматически
                      </span>
                    </div>
                    <div>
                      <button
                        class="btn btn-outline-secondary me-2"
                        @click="resetFields"
                        :disabled="!hasChanges"
                      >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Сбросить
                      </button>
                      <button
                        class="btn btn-primary"
                        @click="saveChanges"
                        :disabled="!hasChanges || saving"
                      >
                        <i class="bi bi-save me-1"></i>
                        <span v-if="saving">
                          <span class="spinner-border spinner-border-sm me-1"></span>
                          Сохранение...
                        </span>
                        <span v-else>Сохранить изменения</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon">🛡️</div>
              <div class="info-content">
                <h4>Безопасность ДВУХ ФАКТОРКУ СДЕЛАТЬ</h4>
                <div class="info-details">
                  <div class="security-status">
                    <span class="status-dot active"></span>
                    <span>Аккаунт активен</span>
                  </div>
                  <div class="security-actions">
                    <button class="btn-outline">Сменить пароль</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCropModal" class="crop-modal-overlay">
      <div class="crop-modal">
        <div class="crop-modal-header">
          <h3>Редактирование изображения</h3>
          <button @click="cancelCrop" class="close-btn">×</button>
        </div>

        <div class="crop-modal-body">
          <div class="crop-instructions">
            <p>Перетащите область выделения для кадрирования изображения</p>
          </div>

          <div class="crop-container">
            <VuePictureCropper
              :img="imageSrc"
              :options="{
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.8,
                dragMode: 'move',
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: true,
              }"
            />
          </div>

          <div class="crop-tools">
            <div class="tool-group">
              <button @click="rotateLeft" class="tool-btn" title="Повернуть влево">
                ↺
              </button>
              <button @click="rotateRight" class="tool-btn" title="Повернуть вправо">
                ↻
              </button>
              <button @click="resetCrop" class="tool-btn" title="Сбросить">
                ⟳
              </button>
            </div>
          </div>
        </div>

        <div class="crop-modal-footer">
          <button @click="cancelCrop" class="btn-outline">
            Отмена
          </button>
          <button @click="crop" class="btn-primary">
            <span class="btn-icon">✂️</span>
            Применить обрезку
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.info-card {
  border-radius: 16px;
  transition: all 0.3s ease;
  background: linear-gradient(145deg, #ffffff, #f8f9fa);
  border: 1px solid rgba(0, 0, 0, 0.08) !important;
}

.info-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12) !important;
}

.info-icon {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.2);
  background: linear-gradient(135deg, var(--bs-primary), #5a6fd8) !important;
}

.form-control, .form-select {
  border-bottom: 2px solid #dee2e6 !important;
  transition: all 0.3s ease;
  background: transparent !important;
  padding-left: 0.75rem !important;
}

.form-control:focus, .form-select:focus {
  border-bottom-color: var(--bs-primary) !important;
  box-shadow: none !important;
  background: rgba(var(--bs-primary-rgb), 0.03) !important;
}

.form-control:disabled, .form-select:disabled {
  background-color: #f8f9fa !important;
  opacity: 0.7;
  cursor: not-allowed;
}

.bg-light {
  background-color: #f8f9fa !important;
}


.form-floating > label {
  padding-left: 0.75rem;
  opacity: 0.8;
  font-weight: 500;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
  color: var(--bs-primary);
  opacity: 1;
  font-weight: 600;
}

.form-floating > label i {
  opacity: 0.7;
}

.form-text {
  padding-left: 0.75rem;
  margin-top: 0.25rem;
  font-size: 0.85rem;
}

.btn-outline-secondary:hover {
  background-color: #f8f9fa;
}

.btn-primary {
  background: linear-gradient(135deg, var(--bs-primary), #5a6fd8);
  border: none;
  padding: 0.5rem 1.25rem;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(var(--bs-primary-rgb), 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.text-success {
  color: #198754 !important;
}

.text-warning {
  color: #fd7e14 !important;
}

.text-danger {
  color: #dc3545 !important;
}

@media (max-width: 768px) {
  .info-card {
    margin: 0;
  }

  .card-body {
    padding: 1.25rem !important;
  }

  .btn {
    width: 100%;
  }

  .d-flex.justify-content-between {
    flex-direction: column;
    gap: 1rem;
  }
}
.user-profile {
  min-height: 100vh;
  padding: 20px;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 80vh;
  color: white;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error {
  max-width: 600px;
  margin: 100px auto;
  background: white;
  border-radius: 12px;
  padding: 40px;
  display: flex;
  align-items: center;
  gap: 25px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.error-icon {
  width: 60px;
  height: 60px;
  background: #ff4757;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  font-weight: bold;
  flex-shrink: 0;
}

.error-content h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.error-content p {
  margin: 0 0 20px 0;
  color: #666;
}

.btn-retry {
  background: #667eea;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}

.profile-container {
  max-width: 1400px;
  margin: 0 auto;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
  display: flex;
  min-height: 800px;
}

.sidebar {
  width: 280px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  display: flex;
  flex-direction: column;
}

.avatar-section {
  text-align: center;
  margin-bottom: 30px;
}

.avatar-wrapper {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto 20px;
}

.user-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid rgba(255, 255, 255, 0.3);
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  font-weight: bold;
  color: white;
  border: 4px solid rgba(255, 255, 255, 0.3);
}

.user-name {
  margin: 0 0 5px 0;
  font-size: 22px;
  font-weight: 600;
}

.user-email {
  margin: 0;
  opacity: 0.9;
  font-size: 14px;
}

.stats-section {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 30px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-item:last-child {
  border-bottom: none;
}

.stat-label {
  font-size: 13px;
  opacity: 0.8;
}

.stat-value {
  font-weight: 500;
}

.stat-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
}

.stat-badge.active {
  background: #10ac84;
}

.logout-btn {
  margin-top: auto;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 12px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-weight: 500;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.logout-icon {
  font-size: 18px;
}

.main-content {
  flex: 1;
  padding: 40px;
  background: #f8f9fa;
}

.content-header {
  margin-bottom: 20px;
}

.content-header h1 {
  margin: 0 0 10px 0;
  color: #333;
  font-size: 28px;
}

.content-header p {
  margin: 0;
  color: #666;
}

.avatar-upload-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.section-header {
  margin-bottom: 25px;
}

.section-header h3 {
  margin: 0 0 8px 0;
  color: #333;
  font-size: 20px;
}

.section-header p {
  margin: 0;
  color: #666;
  font-size: 14px;
}

.upload-container {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}

.current-avatar {
  flex-shrink: 0;
}

.avatar-preview {
  width: 180px;
  height: 180px;
  margin-bottom: 20px;
  position: relative;
}

.new-avatar,
.current-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e9ecef;
}

.avatar-placeholder-large {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 72px;
  font-weight: bold;
  color: white;
  border: 3px solid #e9ecef;
}

.avatar-info h4 {
  margin: 0 0 15px 0;
  color: #333;
}

.avatar-info p {
  margin: 5px 0;
  color: #666;
  font-size: 13px;
}

.upload-controls {
  flex: 1;
}

.file-upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #667eea;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  margin-bottom: 15px;
  transition: background 0.3s;
}

.file-upload-btn:hover {
  background: #5a6fd8;
}

.file-input-hidden {
  display: none;
}

.upload-icon {
  font-size: 20px;
}

.upload-hint {
  color: #666;
  font-size: 13px;
  margin-bottom: 20px;
}

.upload-actions {
  display: flex;
  gap: 15px;
  margin-top: 25px;
}

.btn-primary, .btn-secondary, .btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  border: none;
  transition: all 0.3s;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5a6fd8;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: #e9ecef;
  color: #333;
}

.btn-secondary:hover {
  background: #dee2e6;
}

.btn-outline {
  background: transparent;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-outline:hover {
  background: rgba(102, 126, 234, 0.1);
}

.btn-icon {
  font-size: 18px;
}

.info-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.info-grid {
  display: grid;
  gap: 20px;
  margin-top: 20px;
}

.info-card {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 20px;
  border: 1px solid #e9ecef;
  transition: transform 0.3s, box-shadow 0.3s;
}

.info-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.info-icon {
  font-size: 32px;
  margin-bottom: 15px;
}

.info-content h4 {
  margin: 0 0 15px 0;
  color: #333;
}

.info-details {
  font-size: 14px;
}

.detail-item {
  display: flex;
  margin-bottom: 8px;
}

.detail-label {
  color: #666;
  min-width: 60px;
}

.detail-value {
  color: #333;
  font-weight: 500;
}

.date-display {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.time-ago {
  color: #666;
  font-size: 13px;
}

.security-status {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #10ac84;
}

.status-dot.active {
  background: #10ac84;
}

.crop-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.crop-modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.crop-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 30px;
  border-bottom: 1px solid #e9ecef;
}

.crop-modal-header h3 {
  margin: 0;
  color: #333;
  font-size: 22px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #666;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #333;
}

.crop-modal-body {
  padding: 30px;
  flex: 1;
  overflow-y: auto;
}

.crop-instructions {
  text-align: center;
  color: #666;
  margin-bottom: 20px;
  font-size: 14px;
}

.crop-container {
  height: 400px;
  margin-bottom: 20px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
}

.crop-tools {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.tool-group {
  display: flex;
  gap: 10px;
  background: #f8f9fa;
  padding: 10px;
  border-radius: 8px;
}

.tool-btn {
  width: 40px;
  height: 40px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 18px;
  transition: all 0.2s;
}

.tool-btn:hover {
  background: #e9ecef;
  transform: scale(1.1);
}

.crop-modal-footer {
  padding: 20px 30px;
  border-top: 1px solid #e9ecef;
  display: flex;
  justify-content: flex-end;
  gap: 15px;
}

@media (max-width: 768px) {
  .profile-container {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
  }

  .upload-container {
    flex-direction: column;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .crop-container {
    height: 300px;
  }
}
</style>
