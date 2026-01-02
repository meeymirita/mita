<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/api/axios';

const router = useRouter();
const user = ref(null);
const loading = ref(true);
const error = ref('');
const fetchUserData = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await apiClient.get('/user/me');

    user.value = response.data.data;

  } catch (err) {
    console.error(err);

    if (err.response?.status === 401) {
      error.value = 'Вы не авторизованы';
      setTimeout(() => router.push('/login'), 2000);
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Ошибка при загрузке данных';
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    alert('Требуется авторизация')
    router.push('/login');
    return;
  }
  fetchUserData();
});

const logout = async () => {
  await apiClient.post('/user/logout');
  localStorage.removeItem('auth_token');
  localStorage.removeItem('user_email');
  router.push('/login');
};
</script>

<template>
  <div class="user-profile">
    <div v-if="loading" class="loading">
      Загрузка данных...
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
      <button @click="fetchUserData" class="btn-retry">Повторить</button>
    </div>

    <div v-else-if="user" class="profile-card">
      <div class="profile-header">
        <h2>Профиль пользователя</h2>
        <button @click="logout" class="btn-logout">Выйти</button>
      </div>

      <div class="profile-info">
        <div class="info-item">
          <strong>ID:</strong> {{ user.id }}
        </div>
        <div class="info-item">
          <strong>Имя:</strong> {{ user.name }}
        </div>
        <div class="info-item">
          <strong>Email:</strong> {{ user.email }}
        </div>
        <div class="info-item">
          <strong>Дата регистрации:</strong> {{ new Date(user.created_at).toLocaleDateString() }}
        </div>
      </div>
    </div>

    <!-- Нет данных -->
    <div v-else class="no-data">
      Нет данных пользователя
    </div>
  </div>
</template>

<style scoped>
.user-profile {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.error {
  background: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.btn-retry {
  margin-left: 1rem;
  padding: 0.25rem 0.75rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.profile-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.profile-header h2 {
  margin: 0;
  color: #333;
}

.btn-logout {
  padding: 0.5rem 1rem;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-logout:hover {
  background: #c82333;
}

.profile-info {
  padding: 1.5rem;
}

.info-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item strong {
  display: inline-block;
  width: 180px;
  color: #555;
}

.info-item.warning {
  color: #856404;
  background: #fff3cd;
  padding: 0.75rem;
  border-radius: 6px;
  margin: 0.5rem 0;
}

.btn-verify {
  margin-left: 1rem;
  padding: 0.25rem 0.75rem;
  background: #ffc107;
  color: #333;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.no-data {
  text-align: center;
  padding: 2rem;
  color: #666;
}
</style>
