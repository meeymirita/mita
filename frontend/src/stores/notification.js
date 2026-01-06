import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'

export const useNotificationStore = defineStore('notification', {
  state: () => ({
    toast: null
  }),
  actions: {
    initToast() {
      if (!this.toast) {
        this.toast = useToast()
      }
    },
    success(message, options = {}) {
      this.initToast()
      this.toast.success(message, {
        timeout: 3000,
        position: 'top-right',
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: false,
        closeButton: 'button',
        icon: true,
        rtl: false,
        ...options
      })
    },
    error(message, options = {}) {
      this.initToast()
      this.toast.error(message, {
        timeout: 5000,
        position: 'top-right',
        ...options
      })
    },
    warning(message, options = {}) {
      this.initToast()
      this.toast.warning(message, {
        timeout: 4000,
        position: 'top-right',
        ...options
      })
    },
    info(message, options = {}) {
      this.initToast()
      this.toast.info(message, {
        timeout: 4000,
        position: 'top-right',
        ...options
      })
    },
    avatarUpdated() {
      this.success('Аватар успешно обновлен! 🎉')
    },
    profileSaved() {
      this.success('Профиль успешно сохранен! ✅')
    },
    fileUploadError(message = 'Ошибка загрузки файла') {
      this.error(message)
    },
    unauthorized() {
      this.warning('Требуется авторизация. Пожалуйста, войдите в систему. 🔐')
    },
    // поставить на логин если запустится
    loginSuccess() {
      this.success('Добро пожаловать! 👋')
    },
    logoutSuccess() {
      this.info('Вы успешно вышли из системы. До свидания! 👋')
    },
    // поставить на Email при подтверждении в профиле
    emailSent() {
      this.success('Письмо отправлено! Проверьте вашу почту. 📧')
    },
  }
})
