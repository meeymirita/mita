<script setup>
import apiClient from '@/api/axios.js'
import { ref, onMounted, computed, reactive } from 'vue';

const posts = reactive({
  data: [],
  meta: {},
  links: {}
});
const loading = ref(true);
const currentPage = ref(1);

const getPosts = async (page = 1) => {
  loading.value = true;
  currentPage.value = page;

  try {
    const response = await apiClient.get('/article', {
      params: { page }
    });

    // Laravel
    // data: [...], meta: {...}, links: {...}
    posts.data = response.data.data || [];
    posts.meta = response.data.meta || {};
    posts.links = response.data.links || {};
  } catch (error) {
    console.error('Error fetching posts:', error);
  } finally {
    loading.value = false;
  }
};

const totalPages = computed(() => posts.meta.last_page || 1);
const hasNextPage = computed(() => !!posts.links.next);
const hasPrevPage = computed(() => !!posts.links.prev);
const isFirstPage = computed(() => currentPage.value === 1);
const isLastPage = computed(() => currentPage.value === totalPages.value);
const totalPosts = computed(() => posts.meta.total || 0);
const from = computed(() => posts.meta.from || 0);
const to = computed(() => posts.meta.to || 0);

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value && page !== currentPage.value) {
    getPosts(page);
  }
};

const goToNextPage = () => {
  if (hasNextPage.value) {
    getPosts(currentPage.value + 1);
  }
};

const goToPrevPage = () => {
  if (hasPrevPage.value) {
    getPosts(currentPage.value - 1);
  }
};

const pageNumbers = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  for (let i = startPage; i <= endPage; i++) {
    pages.push(i);
  }

  return pages;
});

onMounted(() => {
  getPosts(1);
});
</script>

<template>
  <div class="row">
    <div class="col-8">
      <div class="posts-container">
        <div v-if="loading" class="loading">
          <div class="spinner"></div>
          <p>Загрузка постов...</p>
        </div>

        <div v-else class="posts-list">
          <div v-if="posts.data.length === 0" class="empty-state">
            <h3>Пока нет постов</h3>
            <p>Будьте первым, кто опубликует что-то интересное!</p>
          </div>

          <div v-for="post in posts.data" :key="post.id" class="post-card">
            <div class="post-header">
              <h2 class="post-title">{{ post.title }}</h2>
              <div v-if="post.author" class="post-author">
                <div class="author-avatar">
                  {{ post.author.login.charAt(0).toUpperCase() }}
                </div>
                <div class="author-info">
                  <span class="author-login">@{{ post.author.login }}</span>
                  <span class="author-type">{{ post.author.type }}</span>
                </div>
              </div>
            </div>

            <div class="post-description">
              <p>{{ post.description }}</p>
            </div>

            <div class="post-meta">
              <div class="meta-item">
                <span class="meta-label">ID:</span>
                <span class="meta-value">{{ post.id }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Создан:</span>
                <span class="meta-value">{{ new Date(post.created_at).toLocaleString() }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-label">Slug:</span>
                <span class="meta-value slug">{{ post.slug }}</span>
              </div>
            </div>

            <div class="post-actions">
              <button class="btn btn-primary">Читать далее</button>
              <button class="btn btn-secondary">Лайк</button>
              <button class="btn btn-outline">Поделиться</button>
            </div>
          </div>
        </div>


        <div v-if="!loading && posts.data.length > 0 && totalPages > 1" class="pagination align-items-center">
          <div class="pagination-info">
            Показано с {{ from }} по {{ to }} из {{ totalPosts }} постов
          </div>

          <div class="pagination-controls">
            <button
              @click="goToPage(1)"
              :disabled="isFirstPage"
              class="pagination-btn"
              :class="{ 'disabled': isFirstPage }"
              title="Первая страница"
            >
              &laquo;
            </button>

            <button
              @click="goToPrevPage"
              :disabled="!hasPrevPage"
              class="pagination-btn"
              :class="{ 'disabled': !hasPrevPage }"
              title="Предыдущая страница"
            >
              &lsaquo;
            </button>

            <div class="page-numbers">
              <button
                v-for="page in pageNumbers"
                :key="page"
                @click="goToPage(page)"
                class="page-number"
                :class="{ 'active': page === currentPage }"
              >
                {{ page }}
              </button>
            </div>

            <button
              @click="goToNextPage"
              :disabled="!hasNextPage"
              class="pagination-btn"
              :class="{ 'disabled': !hasNextPage }"
              title="Следующая страница"
            >
              &rsaquo;
            </button>

            <button
              @click="goToPage(totalPages)"
              :disabled="isLastPage"
              class="pagination-btn"
              :class="{ 'disabled': isLastPage }"
              title="Последняя страница"
            >
              &raquo;
            </button>
          </div>

          <div class="page-jump">
            <span class="jump-label">Страница:</span>
            <div class="jump-controls">
              <input
                type="number"
                :value="currentPage"
                @change="(e) => goToPage(parseInt(e.target.value) || 1)"
                :min="1"
                :max="totalPages"
                class="page-input"
              >
              <span class="jump-total">из {{ totalPages }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-2">
      <p>фильтры</p>
    </div>
  </div>
</template>

<style scoped>
.posts-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 20px;
}

.loading {
  text-align: center;
  padding: 40px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.post-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #eaeaea;
  transition: all 0.3s ease;
}

.post-card:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
  gap: 20px;
  flex-direction: row-reverse;
}

.post-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
  flex: 1;
}

.post-author {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 150px;
}

.author-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
}

.author-info {
  display: flex;
  flex-direction: column;
}

.author-login {
  font-weight: 600;
  color: #3498db;
}

.author-type {
  font-size: 0.8rem;
  color: #7f8c8d;
  text-transform: capitalize;
}

.post-description {
  margin: 20px 0;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #3498db;
}

.post-description p {
  margin: 0;
  color: #34495e;
  line-height: 1.6;
}

.post-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin: 20px 0;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.meta-label {
  font-weight: 600;
  color: #7f8c8d;
  font-size: 0.9rem;
}

.meta-value {
  color: #2c3e50;
  font-weight: 500;
}

.meta-value.slug {
  font-family: monospace;
  background: #f1f1f1;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.9rem;
}

.post-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eaeaea;
}

.btn {
  padding: 10px 20px;
  border-radius: 6px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover {
  background: #2980b9;
  transform: translateY(-1px);
}

.btn-secondary {
  background: #e74c3c;
  color: white;
}

.btn-secondary:hover {
  background: #c0392b;
}

.btn-outline {
  background: transparent;
  border: 2px solid #3498db;
  color: #3498db;
}

.btn-outline:hover {
  background: #3498db;
  color: white;
}
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #7f8c8d;
}

.empty-state h3 {
  font-size: 1.8rem;
  margin-bottom: 10px;
  color: #34495e;
}

.empty-state p {
  font-size: 1.1rem;
}

.pagination {
  margin-top: 40px;
  padding: 25px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  border: 1px solid #eaeaea;
}

.pagination-info {
  text-align: center;
  margin-bottom: 20px;
  color: #7f8c8d;
  font-size: 0.95rem;
  font-weight: 500;
  padding: 10px;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.pagination-btn {
  padding: 10px 16px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  color: #3498db;
  transition: all 0.2s ease;
  min-width: 44px;
  text-align: center;
  font-size: 1rem;
}

.pagination-btn:hover:not(.disabled) {
  background: #e9ecef;
  border-color: #3498db;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(52, 152, 219, 0.2);
}

.pagination-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 4px;
  margin: 0 10px;
}

.page-number {
  padding: 10px 16px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  color: #555;
  transition: all 0.2s ease;
  min-width: 44px;
  text-align: center;
}

.page-number:hover {
  background: #e9ecef;
  border-color: #3498db;
  color: #3498db;
}

.page-number.active {
  background: #3498db;
  border-color: #3498db;
  color: white;
}

.page-number.active:hover {
  background: #2980b9;
  border-color: #2980b9;
}

/* Переход к странице */
.page-jump {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding: 10px;
}

.jump-label {
  font-weight: 500;
  color: #7f8c8d;
  font-size: 0.95rem;
}

.jump-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-input {
  width: 60px;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  text-align: center;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}

.page-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.jump-total {
  font-size: 0.9rem;
  color: #7f8c8d;
}

/* Адаптивность */
@media (max-width: 768px) {
  .posts-container {
    padding: 10px;
  }

  .post-header {
    flex-direction: column;
    gap: 15px;
  }

  .post-author {
    align-self: flex-start;
  }

  .post-meta {
    flex-direction: column;
    gap: 10px;
  }

  .post-actions {
    flex-wrap: wrap;
  }

  .btn {
    flex: 1;
    min-width: 120px;
  }

  .pagination-controls {
    flex-direction: column;
    gap: 10px;
  }

  .page-numbers {
    order: -1;
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }

  .page-jump {
    flex-direction: column;
    gap: 10px;
  }

  .jump-controls {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .post-card {
    padding: 15px;
  }

  .post-title {
    font-size: 1.2rem;
  }

  .post-description p {
    font-size: 0.9rem;
  }

  .btn {
    padding: 8px 12px;
    font-size: 0.85rem;
  }

  .pagination-btn,
  .page-number {
    padding: 8px 12px;
    min-width: 36px;
  }
}
</style>
