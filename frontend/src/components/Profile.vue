<template>
  <div class="space-y-6">
    
    <div class="bg-gradient-to-r from-cyan-50/80 to-blue-50/80 border border-slate-100 rounded-2xl p-6 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
      <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white flex items-center justify-center font-black text-3xl shadow-lg shadow-cyan-200 shrink-0">
        {{ userData.name ? userData.name.charAt(0).toUpperCase() : 'U' }}
      </div>

      <div class="text-center sm:text-left flex-1 min-w-0 space-y-1">
        <div class="flex items-center justify-center sm:justify-start space-x-2">
          <h2 class="text-xl font-bold text-slate-800 truncate">{{ userData.name || 'Đang tải thông tin...' }}</h2>
          <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-emerald-100 text-emerald-700">
            Đang hoạt động
          </span>
        </div>
        <p class="text-sm text-slate-500 truncate">{{ userData.email || 'email@example.com' }}</p>
      </div>
    </div>

    <div class="space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
        Thông tin cá nhân
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Họ và tên</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input 
              type="text" 
              :value="userData.name" 
              readonly 
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium focus:outline-none cursor-default"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Địa chỉ Email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <input 
              type="email" 
              :value="userData.email" 
              readonly 
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium focus:outline-none cursor-default"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Ngày tham gia</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <input 
              type="text" 
              :value="formatDate(userData.created_at)" 
              readonly 
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium focus:outline-none cursor-default"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
      <router-link 
        to="/tasks" 
        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition cursor-pointer flex items-center space-x-1"
      >
        <span>← Quay lại</span>
      </router-link>

      <span class="text-xs text-slate-400 font-medium">Phiên bản 1.0.0</span>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, inject, computed } from 'vue'

interface UserProfile {
  id: number | null
  name: string
  email: string
  created_at: string
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return ''
  return dateStr.split('T')[0] ?? ''
}

const userRef = inject<any>('user', ref<UserProfile>({
  id: null,
  name: '',
  email: '',
  created_at: ''
}))

const userData = computed<UserProfile>(() => {
  return userRef.value || { id: null, name: '', email: '', created_at: '' }
})
</script>
