<template>
  <div style="text-align: center; margin-top: 50px">
    <p>Đang đăng nhập, vui lòng chờ trong giây lát...</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

onMounted(() => {
  const token = route.query.token;

  if (token) {
    localStorage.setItem('token', token.toString());
    router.push('/tasks');
  } else {
    router.push('/login');
  }
});
</script>
