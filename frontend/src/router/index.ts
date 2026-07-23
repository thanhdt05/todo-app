import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import TasksList from '@/components/TasksList.vue'

const routes = [
  {
    path: '/',
    redirect: '/tasks'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/tasks',
    name: 'TasksList',
    component: TasksList
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
