import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/components/Login.vue';
import Register from '@/components/Register.vue';
import TasksList from '@/components/TasksList.vue';
import TaskLayout from '@/components/TaskLayout.vue';
import TrashedTasksList from '@/components/TrashedTasksList.vue';
import Profile from '@/components/Profile.vue';
import AuthCallback from '@/components/AuthCallback.vue';

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/auth/callback',
    name: 'AuthCallback',
    component: AuthCallback,
  },
  {
    path: '/tasks',
    name: 'TaskLayout',
    component: TaskLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'profile',
        name: 'Profile',
        component: Profile,
      },
      {
        path: '',
        name: 'TasksList',
        component: TasksList,
      },
      {
        path: 'trash',
        name: 'TrashedTasksList',
        component: TrashedTasksList,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const token = localStorage.getItem('token');

  if (to.matched.some((record) => record.meta.requiresAuth) && !token) {
    return { name: 'Login' };
  }

  if ((to.name === 'Login' || to.name === 'Register') && token) {
    return { name: 'TasksList' };
  }
});

export default router;
