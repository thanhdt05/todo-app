<template>
  <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200 my-4">
    <p class="text-slate-500 font-medium text-sm">
      {{ message || 'Không có công việc nào.' }}
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{ message?: string }>();
</script>
