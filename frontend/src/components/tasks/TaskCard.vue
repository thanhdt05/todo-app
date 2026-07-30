<template>
  <li
    @click="$emit('click-card', task)"
    class="flex items-center justify-between px-4 py-3.5 bg-[#f4f6f7] rounded-xl hover:bg-[#eaeef1] transition cursor-pointer"
  >
    <!-- Left: Checkbox & Title -->
    <div class="flex items-center space-x-3.5 flex-1 min-w-0 pr-3">
      <input
        v-if="!isTrashed"
        type="checkbox"
        :checked="task.status === 'done'"
        :disabled="task.status === 'done'"
        @click.stop
        @change="emit('toggle-complete', task)"
        class="w-5 h-5 accent-blue-600 rounded shrink-0"
        :class="task.status === 'done' ? 'cursor-not-allowed opacity-70' : 'cursor-pointer'"
      />
      <input
        v-else
        type="checkbox"
        :checked="isSelected"
        @click.stop
        @change="emit('toggle-select', task.id)"
        class="w-5 h-5 accent-cyan-600 rounded shrink-0 cursor-pointer"
      />
      <span
        :class="
          task.status === 'done'
            ? 'line-through text-slate-400'
            : 'text-sm sm:text-base text-slate-700 font-medium'
        "
        class="truncate"
      >
        {{ task.title }}
      </span>
    </div>

    <div class="mx-3 shrink-0">
      <span
        class="px-2.5 py-1 text-xs font-bold rounded-full uppercase"
        :class="{
          'bg-amber-100 text-amber-700': task.status === 'todo',
          'bg-blue-100 text-blue-700': task.status === 'doing',
          'bg-emerald-100 text-emerald-700': task.status === 'done',
        }"
      >
        {{ task.status }}
      </span>
    </div>

    <div v-if="task.due_date" class="mx-2 shrink-0">
      <span
        class="text-xs flex items-center space-x-1"
        :class="
          task.is_overdue && task.status !== 'done'
            ? 'text-red-500 font-semibold'
            : 'text-slate-400'
        "
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3.5 w-3.5 inline"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <span>{{ formattedDate }}</span>
      </span>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center space-x-1 shrink-0">
      <template v-if="!isTrashed">
        <button
          type="button"
          @click.stop="emit('edit', task)"
          class="p-1.5 text-cyan-500 hover:text-cyan-600 hover:bg-cyan-50 rounded-lg transition cursor-pointer"
          title="Sửa công việc"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            />
          </svg>
        </button>

        <button
          type="button"
          @click.stop="emit('delete', task)"
          class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer"
          title="Xóa công việc"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
        </button>
      </template>

      <template v-if="isTrashed">
        <button
          type="button"
          @click.stop="emit('restore', task)"
          class="p-1.5 text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition cursor-pointer"
          title="Khôi phục công việc"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
        </button>
        <button
          type="button"
          @click.stop="emit('force-delete', task)"
          class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer"
          title="Xóa công việc"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
        </button>
      </template>
    </div>
  </li>
</template>

<script setup lang="ts">
import type { Task } from '@/types/tasks';
import { computed } from 'vue';

interface Props {
  task: Task;
  isTrashed?: boolean;
  isSelected?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isTrashed: false,
  isSelected: false,
});

const emit = defineEmits<{
  (e: 'click-card', task: Task): void;
  (e: 'toggle-complete', task: Task): void;
  (e: 'toggle-select', taskId: number): void;
  (e: 'edit', task: Task): void;
  (e: 'delete', task: Task): void;
  (e: 'restore', task: Task): void;
  (e: 'force-delete', task: Task): void;
}>();

const formattedDate = computed(() => {
  if (!props.task.due_date) return '';
  return props.task.due_date.split('T')[0]?.split(' ')[0] ?? '';
});
</script>
