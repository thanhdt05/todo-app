<template>
  <div
    class="flex items-center space-x-6 border-b border-slate-200 mb-6 text-xs sm:text-sm font-bold uppercase tracking-wider"
  >
    <button
      v-for="tab in tabs"
      :key="tab"
      type="button"
      @click="$emit('update:modelValue', tab)"
      :class="
        modelValue === tab
          ? 'text-blue-600 border-b-2 border-blue-600'
          : 'text-slate-400 hover:text-slate-600'
      "
      class="pb-3 transition cursor-pointer"
    >
      {{ tab }}
    </button>
  </div>
</template>

<script setup lang="ts">
import type { TaskTab } from '@/types/tasks';

defineProps<{ modelValue: TaskTab }>();
defineEmits<{ (e: 'update:modelValue', tab: TaskTab): void }>();

const tabs: TaskTab[] = ['ALL', 'TODO', 'DOING', 'DONE'];
</script>
