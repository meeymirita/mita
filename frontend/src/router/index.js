import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/components/Layout.vue';
import HomePage from "@/pages/Home.vue";
import Register from '@/components/Register.vue';
import Login from '@/components/Login.vue';
import Logout from '@/components/Logout.vue'
const routes = [
  {
    path: "/",
    component: Layout,
    children: [
      {
        path: "",
        name: "home",
        component: HomePage,
      },
      {
        path: "register",
        name: "register",
        component: Register,
      },
      {
        path: "login",
        name: "login",
        component: Login,
      },
      {
        path: "logout",
        name: "profile",
        component: Logout,
      },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
