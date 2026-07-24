import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import Register from '../components/Register.vue'
import TasksList from '@/components/TasksList.vue'
import TaskLayout from '@/components/TaskLayout.vue'
import TrashedTasksList from '@/components/TrashedTasksList.vue'
import Profile from '@/components/Profile.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
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
    name: 'TaskLayout',
    component: TaskLayout,
    children: [
      {
        path: 'profile',
        name: 'Profile',
        component: Profile
      },
      {
        path: '',
        name: 'TasksList',
        component: TasksList
      },
      {
        path: 'trash',
        name: 'TrashedTasksList',
        component: TrashedTasksList
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
