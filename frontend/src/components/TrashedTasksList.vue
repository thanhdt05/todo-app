<template>
  <div>
    <div v-if="errorMessage" class="mb-4 p-3.5 bg-red-50 text-red-700 text-sm rounded-xl">
      {{ errorMessage }}
    </div>

    <div v-if="successMessage" class="mb-4 p-3.5 bg-emerald-50 text-emerald-700 text-sm rounded-xl">
      {{ successMessage }}
    </div>

    <TaskSearch v-model="keyword" placeholder="Tìm kiếm công việc trong thùng rác..." />

    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
      <label
        class="flex items-center space-x-2 text-sm text-slate-600 font-semibold cursor-pointer"
      >
        <input
          type="checkbox"
          :checked="isSelectAll"
          @change="toggleSelectAll"
          class="w-4 h-4 accent-cyan-600 rounded cursor-pointer"
        />
        <span>Chọn tất cả ({{ selectedTaskIds.length }}/{{ tasks.length }})</span>
      </label>

      <button
        v-if="selectedTaskIds.length > 0"
        type="button"
        @click="handleBulkRestore"
        class="px-3.5 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white font-bold text-xs rounded-xl shadow-md cursor-pointer"
      >
        Khôi phục ({{ selectedTaskIds.length }})
      </button>
    </div>

    <TaskEmpty v-if="!isLoading && tasks.length === 0" message="Thùng rác trống." />

    <ul v-else class="space-y-2.5">
      <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        :is-trashed="true"
        :is-selected="selectedTaskIds.includes(task.id)"
        @toggle-select="toggleSelectTask"
        @restore="restoreTask"
        @force-delete="handleOpenForceDeleteModal"
      />
    </ul>

    <Pagination
      v-if="lastPage > 1"
      :current-page="currentPage"
      :total-pages="lastPage"
      :total="totalTasks"
      @change="gotoPage"
    />

    <TaskDeleteModal
      :is-open="isDeleteModalOpen"
      :is-force-delete="true"
      @close="isDeleteModalOpen = false"
      @confirm="handleConfirmForceDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import TaskSearch from './TaskSearch.vue';
import Pagination from './Pagination.vue';
import TaskCard from './tasks/TaskCard.vue';
import TaskDeleteModal from './tasks/TaskDeleteModal.vue';
import TaskEmpty from './tasks/TaskEmpty.vue';

import { useTasks } from '@/composables/useTasks';
import { taskApi } from '@/services/taskApi';
import type { Task } from '@/types/tasks.ts';

const {
  tasks,
  keyword,
  currentPage,
  lastPage,
  totalTasks,
  isLoading,
  errorMessage,
  successMessage,
  gotoPage,
  restoreTask,
  forceDeleteTask,
  fetchTasks,
} = useTasks(true);

const selectedTaskIds = ref<number[]>([]);

const isSelectAll = computed(() => {
  return tasks.value.length > 0 && selectedTaskIds.value.length === tasks.value.length;
});

const toggleSelectAll = () => {
  if (isSelectAll.value) {
    selectedTaskIds.value = [];
  } else {
    selectedTaskIds.value = tasks.value.map((t) => t.id);
  }
};

const toggleSelectTask = (taskId: number) => {
  const index = selectedTaskIds.value.indexOf(taskId);
  if (index > -1) {
    selectedTaskIds.value.splice(index, 1);
  } else {
    selectedTaskIds.value.push(taskId);
  }
};

const handleBulkRestore = async () => {
  try {
    const res = await taskApi.bulkRestore(selectedTaskIds.value);
    successMessage.value = res.data.message || 'Đã khôi phục các mục đã chọn';
    selectedTaskIds.value = [];
    await fetchTasks(currentPage.value);
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Khôi phục thất bại';
  }
};

const isDeleteModalOpen = ref(false);
const taskToDelete = ref<Task | null>(null);

const handleOpenForceDeleteModal = (task: Task) => {
  taskToDelete.value = task;
  isDeleteModalOpen.value = true;
};

const handleConfirmForceDelete = async () => {
  if (taskToDelete.value) {
    await forceDeleteTask(taskToDelete.value);
    isDeleteModalOpen.value = false;
    taskToDelete.value = null;
  }
};
</script>
