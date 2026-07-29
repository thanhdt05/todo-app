<template>
  <div class="flex items-center justify-center min-h-screen">
    <p class="text-slate-500 font-medium">Đang xử lý đăng nhập...</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

onMounted(() => {
  const token = route.query.token as string;

  if (token) {
    localStorage.setItem('token', token);
    router.replace('/tasks');
  } else {
    router.replace('/login');
  }
});
</script>
