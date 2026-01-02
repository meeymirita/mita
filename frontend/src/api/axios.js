import axios from 'axios'
const apiClient = axios.create({
  baseURL: 'http://localhost:8080/api', // Laravel API
  timeout: 10000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
});

// Перехватчик запросов
apiClient.interceptors.request.use(
  config => {
    // токен авторизации
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);
//
// apiClient.interceptors.response.use(
//   response => response,
//   error => {
//     // Обработка ошибок 401 (Unauthorized)
//     if (error.response?.status === 401) {
//       localStorage.removeItem('token');
//       window.location.href = '/login';
//     }
//     return Promise.reject(error);
//   }
// );

export default apiClient;
