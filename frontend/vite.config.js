import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  // ДОБАВЬТЕ ЭТО для Docker:
  server: {
    host: '0.0.0.0',  // Это важно для Docker!
    port: 5173,
    allowedHosts: ['meeymirita.ru', 'www.meeymirita.ru'],
    strictPort: true,  // Не менять порт если занят
    watch: {
      usePolling: true  // Для hot reload в Docker
    },
    hmr: {
      host: 'localhost',
      port: 5173
    }
  }
})
