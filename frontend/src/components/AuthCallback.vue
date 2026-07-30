<template>
  <div class="flex min-h-screen items-center justify-center">
    <p class="font-medium text-slate-500">Đang xử lý đăng nhập...</p>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';

const route = useRoute();
const router = useRouter();

function getQueryString(value: unknown): string | null {
  if (typeof value === 'string' && value.trim() !== '') {
    return value;
  }

  return null;
}

async function redirectToLogin(message: string): Promise<void> {
  await router.replace({
    name: 'Login',
    query: { error: message },
  });
}

async function completeLogin(token: string): Promise<void> {
  localStorage.setItem('token', token);
  await router.replace({ name: 'TasksList' });
}

onMounted(async () => {
  const exchangeCode = getQueryString(route.query.exchange_code);

  if (!exchangeCode) {
    await redirectToLogin('Không nhận được mã xác thực đăng nhập');
    return;
  }

  try {
    const response = await api.post('/auth/social/exchange', {
      code: exchangeCode,
    });

    const token = response.data?.data?.token;

    if (typeof token !== 'string' || token.trim() === '') {
      await redirectToLogin('Máy chủ không trả về token đăng nhập');
      return;
    }

    await completeLogin(token);
  } catch (error: unknown) {
    console.error('Lỗi khi đổi exchange code:', error);

    const message = axios.isAxiosError(error) ? error.response?.data?.message : null;

    await redirectToLogin(typeof message === 'string' ? message : 'Không thể xác thực tài khoản');
  }
});
</script>
