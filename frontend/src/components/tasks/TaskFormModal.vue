<template>
  <div
    v-if="isModalOpen"
    class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4"
  >
    <div
      class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 border border-slate-100"
    >
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-lg font-bold text-slate-800">{{ title }}</h3>
        <button
          @click="emit('close')"
          class="text-slate-400 hover:text-slate-600 font-bold text-xl cursor-pointer"
        >
          &times;
        </button>
      </div>

      <!-- Modal Error Alert -->
      <div
        v-if="errorMessage"
        class="p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl flex items-center justify-between"
      >
        <div class="flex items-center space-x-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-red-500 shrink-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span class="font-medium">{{ errorMessage }}</span>
        </div>
        <button
          type="button"
          @click="errorMessage = ''"
          class="text-red-400 hover:text-red-600 font-bold text-lg cursor-pointer ml-2"
        >
          &times;
        </button>
      </div>

      <form @submit.prevent="emit('submit')" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase mb-1"
            >Tiêu đề (*)</label
          >
          <input
            v-model="taskForm.title"
            :disabled="!isEditMode"
            type="text"
            placeholder="Nhập tiêu đề công việc..."
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Mô tả</label>
          <textarea
            v-model="taskForm.description"
            :disabled="!isEditMode"
            placeholder="Nhập mô tả chi tiết..."
            rows="3"
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
          ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1"
              >Hạn hoàn thành</label
            >
            <input
              v-model="taskForm.due_date"
              :disabled="!isEditMode"
              type="date"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1"
              >Trạng thái</label
            >
            <select
              v-model="taskForm.status"
              :disabled="!isEditMode"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 text-sm"
            >
              <option value="todo">Todo</option>
              <option value="doing">Doing</option>
              <option value="done">Done</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
          <button
            v-if="!isEditMode"
            type="button"
            @click="emit('enable-edit')"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm shadow-md"
          >
            Chỉnh sửa thông tin
          </button>
          <button
            v-if="isEditMode"
            type="button"
            @click="emit('close')"
            class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-xl font-medium text-sm transition cursor-pointer"
          >
            Hủy
          </button>
          <button
            v-if="isEditMode"
            type="submit"
            class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-xl text-sm shadow-md transition cursor-pointer"
          >
            Lưu công việc
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { TaskPayload } from '@/types/tasks';

const props = defineProps<{
  isModalOpen: boolean;
  isEditMode: boolean;
  isSubmitting?: boolean;
  title: string;
  errorMessage?: string;
  taskForm: TaskPayload;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'submit'): void;
  (e: 'enable-edit'): void;
}>();
</script>
