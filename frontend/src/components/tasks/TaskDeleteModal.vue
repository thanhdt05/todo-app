<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4"
  >
    <div
      class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl space-y-4 border border-slate-100 text-center"
    >
      <div
        class="w-12 h-12 rounded-full bg-red-100 text-red-500 mx-auto flex items-center justify-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
      </div>

      <h3 class="text-lg font-bold text-slate-800">
        {{ isForceDelete ? 'Xác nhận xóa vĩnh viễn' : 'Xác nhận xóa' }}
      </h3>

      <p class="text-sm text-slate-600">
        Bạn có chắc chắn muốn {{ isForceDelete ? 'xóa vĩnh viễn' : 'xóa' }}?
      </p>

      <div class="flex items-center justify-center space-x-3 pt-2">
        <button
          type="button"
          @click="emit('close')"
          class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold text-sm transition cursor-pointer"
        >
          Hủy
        </button>
        <button
          type="button"
          @click="emit('confirm')"
          class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-sm shadow-md transition cursor-pointer"
        >
          {{ isForceDelete ? 'Xóa vĩnh viễn' : 'Xóa' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  isOpen: boolean;
  isForceDelete?: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'confirm'): void;
}>();
</script>
