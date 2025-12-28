import { createRouter, createWebHistory } from 'vue-router'
import HomePage from "@/pages/Home.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: "/",
      name: "Главная",
      component: HomePage,
    },
    {
      path: "/register",
      name: "Регистрация",
      component: () => import("@/pages/RegistrationPage.vue"),
    },

  ],

});

export default router;
