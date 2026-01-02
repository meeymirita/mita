<script setup>
import { ref, reactive } from 'vue';
import apiClient from '@/api/axios';

const form = reactive({
  email: '',
  password: '',
  password_confirmation: '',
});

const registerForm = ref(true);
const verifyCode = ref(false);

const handleSubmit = async () => {
  try {
    const response = await apiClient.post('/user/register', {
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation
    });

    if (response.data.data?.success === true) {
      alert('Сообщение ушло на почту');
      localStorage.setItem('user_email', response.data.data.user.email);

      if (response.data.data.token) {
        localStorage.setItem('auth_token', response.data.data.token);
        alert('Токен авторизации');
      }
      registerForm.value = false;
      verifyCode.value = true;
    }
  } catch (error) {
    console.error('Ошибка регистрации:', error);
  }
};

const verifyEmailCode = async () => {

  try {
    const response = await apiClient.post('/user/verify-email', {
      email: localStorage.getItem('user_email'),
      code: form.code,
    });

    if (response.data.success === true) {
      alert(`${response.data.message}`);

    } else if (response.data.message) {
      alert(`ℹ️ ${response.data.message}`);
    } else {
      alert('Email подтвержден');
    }

  } catch (error) {
    console.error(error);

  } finally {
    // localStorage.clear();
  }
};
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <div class="text-center mb-4">
        <h2 class="register-title">Регистрация</h2>
      </div>

      <!-- Форма регистрации -->
      <form v-if="registerForm" class="register-form" @submit.prevent="handleSubmit">
        <div class="form-group">
          <label class="form-label">Email адрес</label>
          <div class="input-group">
            <span class="input-icon">📧</span>
            <input
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="you@example.com"
              required
            >
          </div>
          <br>
          <span style="font-size: 12px; color: #666;">Пример: you@mail.com</span>
        </div>

        <div class="form-group">
          <label class="form-label">Пароль</label>
          <div class="input-group">
            <span class="input-icon">🔒</span>
            <input
              v-model="form.password"
              type="password"
              class="form-control"
              placeholder="Создайте пароль"
              required
            >
          </div>
          <span style="font-size: 12px; color: #666;">
            Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру
          </span>
          <br>
          <span style="font-size: 12px; color: #666;">Пример: qweqweQQ123Q</span>
        </div>

        <div class="form-group">
          <label class="form-label">Подтверждение пароля</label>
          <div class="input-group">
            <span class="input-icon">✓</span>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="form-control"
              placeholder="Повторите пароль"
              required
            >
          </div>
        </div>

        <button type="submit" class="submit-btn" >
          клик →
        </button>

        <div class="text-center mt-3">
          <p class="login-link">
            Уже есть аккаунт?
            <router-link to="/login" class="link">Войдите</router-link>
          </p>
        </div>
      </form>

      <!-- Форма подтверждения email -->
      <form v-if="verifyCode" class="register-form" @submit.prevent="verifyEmailCode">
        <div class="text-center mb-4">
          <h3 class="verify-title">Подтверждение email</h3>
          <p class="verify-subtitle">
<!--            Код отправлен на {{ localStorage.getItem('user_email') ?? '' }}-->
          </p>
        </div>

        <div class="form-group">
          <label class="form-label">Код подтверждения</label>
          <div class="input-group">
            <span class="input-icon">🔢</span>
            <input
              v-model="form.code"
              class="form-control"
              placeholder="Введите 6-значный код"
              required
              maxlength="6"
            >
          </div>
          <span style="font-size: 12px; color: #666; margin-top: 5px; display: block;">
            Введите код, который пришел на ваш email
          </span>
        </div>

        <button type="submit" class="submit-btn" >
          клик →
        </button>

        <div class="text-center mt-3">
          <p class="resend-link">
            Не получили код?
            <a href="#" @click.prevent="resendCode" class="link">Отправить повторно</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
/* Стили для формы подтверждения */
.verify-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 0.5rem;
}

.verify-subtitle {
  color: #718096;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.resend-link {
  color: #718096;
  font-size: 0.9rem;
  margin-top: 1rem;
}

.back-link {
  margin-top: 0.5rem;
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.form-control:focus {
  box-shadow: none !important;
  border-color: #dee2e6 !important;
  outline: none !important;
}

.btn:focus {
  box-shadow: none !important;
  outline: none !important;
}
.register-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  font-family: system-ui, -apple-system, sans-serif;
}

.register-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  width: 100%;
  max-width: 1000px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.register-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 0.5rem;
}

.register-subtitle {
  color: #718096;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 500;
  color: #2d3748;
  font-size: 0.9rem;
}

.input-group {
  display: flex;
  align-items: center;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  transition: border-color 0.2s;
  background: #f8fafc;
}

.input-group:focus-within {
  border-color: #4299e1;
  background: white;
}

.input-icon {
  padding: 0 0.75rem;
  color: #a0aec0;
  font-size: 1rem;
}

.form-control {
  flex: 1;
  padding: 0.75rem;
  border: none;
  background: transparent;
  font-size: 0.95rem;
  color: #2d3748;
  outline: none;
}

.form-control::placeholder {
  color: #cbd5e0;
}

.password-toggle {
  padding: 0 0.75rem;
  border: none;
  background: transparent;
  color: #a0aec0;
  cursor: pointer;
  font-size: 1rem;
}

.form-check-input {
  margin-right: 0.5rem;
  vertical-align: middle;
}

.form-check-label {
  font-size: 0.9rem;
  color: #4a5568;
}

.link {
  color: #4299e1;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

.submit-btn {
  width: 100%;
  padding: 0.75rem;
  background: #4299e1;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.submit-btn:hover {
  background: #3182ce;
}

.divider {
  text-align: center;
  margin: 1rem 0;
  color: #a0aec0;
  font-size: 0.9rem;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e2e8f0;
  z-index: 1;
}

.divider {
  background: white;
  padding: 0 1rem;
  position: relative;
  z-index: 2;
  display: inline-block;
  margin: 1rem auto;
}

.social-buttons {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.social-btn {
  flex: 1;
  padding: 0.6rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-size: 0.9rem;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s;
}

.social-btn:hover {
  border-color: #4299e1;
}

.google-btn {
  background: #f8fafc;
}

.github-btn {
  background: #f8fafc;
}

.login-link {
  color: #718096;
  font-size: 0.9rem;
}

@media (max-width: 480px) {
  .register-card {
    padding: 1.5rem;
  }

  .social-buttons {
    flex-direction: column;
  }
}
</style>
