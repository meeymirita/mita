import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/components/Layout.vue';
import HomePage from "@/pages/HomePage.vue";
import Register from '@/pages/RegisterPage.vue';
import Login from '@/pages/LoginPage.vue';
import Logout from '@/pages/Logout.vue'
import Me from '@/pages/MePage.vue'
import AllPosts from '@/pages/PostsPage.vue'
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
      {
        path: "me",
        name: "me",
        component: Me,
      },
      {
        path: "posts",
        name: "posts",
        component: AllPosts,
      },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
