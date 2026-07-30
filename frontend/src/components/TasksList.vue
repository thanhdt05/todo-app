<template>
  <div>
    <div class="flex items-center justify-between">
      <TaskSearch v-model="keyword" placeholder="Tìm kiếm công việc..." class="flex-grow" />
      <button
        type="button"
        @click="openCreateModal"
        class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-3xl shadow-md transition cursor-pointer mb-5"
      >
        + Thêm Task
      </button>
    </div>

    <div
      v-if="listErrorMessage && !isModalOpen"
      class="mb-4 p-3.5 bg-red-50 text-red-700 text-sm rounded-xl"
    >
      {{ listErrorMessage }}
    </div>

    <div
      v-if="successMessage && !isModalOpen"
      class="mb-4 p-3.5 bg-emerald-50 text-emerald-700 text-sm rounded-xl"
    >
      {{ successMessage }}
    </div>

    <TaskFilters v-model="currentTab" />

    <TaskEmpty
      v-if="!isLoading && tasks.length === 0"
      message="Không tìm thấy công việc phù hợp."
    />

    <ul v-else class="space-y-2.5">
      <TaskCard
        v-for="task in tasks"
        :key="task.id"
        :task="task"
        @click-card="openDetailModal"
        @toggle-complete="toggleComplete"
        @edit="openEditModal"
        @delete="handleOpenDeleteModal"
      />
    </ul>

    <Pagination
      v-if="lastPage > 1"
      :current-page="currentPage"
      :total-pages="lastPage"
      :total="totalTasks"
      @change="gotoPage"
    />

    <TaskFormModal
      :is-modal-open="isModalOpen"
      :is-edit-mode="isEditMode"
      :is-submitting="isSubmitting"
      :title="modalTitle"
      :error-message="formErrorMessage"
      :task-form="taskForm"
      @close="closeModal"
      @submit="submitForm"
      @enable-edit="isEditMode = true"
    />

    <TaskDeleteModal
      :is-open="isDeleteModalOpen"
      @close="isDeleteModalOpen = false"
      @confirm="handleConfirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import TaskSearch from './TaskSearch.vue';
import Pagination from './Pagination.vue';
import TaskCard from './tasks/TaskCard.vue';
import TaskFilters from './tasks/TaskFilters.vue';
import TaskFormModal from './tasks/TaskFormModal.vue';
import TaskDeleteModal from './tasks/TaskDeleteModal.vue';
import TaskEmpty from './tasks/TaskEmpty.vue';

import { useTasks } from '@/composables/useTasks';
import { useTaskForm } from '@/composables/useTaskForm';
import type { Task } from '@/types/tasks.ts';

const {
  tasks,
  keyword,
  currentTab,
  currentPage,
  lastPage,
  totalTasks,
  isLoading,
  errorMessage: listErrorMessage,
  successMessage,

  fetchTasks,
  gotoPage,
  toggleComplete,
  deleteTask,
  forceDeleteTask,
  restoreTask,
} = useTasks(false);

const {
  isModalOpen,
  isEditMode,
  isSubmitting,
  selectedTaskId,
  taskForm,
  modalTitle,
  errorMessage: formErrorMessage,

  openCreateModal,
  openDetailModal,
  openEditModal,
  closeModal,
  formatDate,
  submitForm,
} = useTaskForm(() => fetchTasks(currentPage.value));

const isDeleteModalOpen = ref(false);
const taskToDelete = ref<Task | null>(null);

const handleOpenDeleteModal = (task: Task) => {
  taskToDelete.value = task;
  isDeleteModalOpen.value = true;
};

const handleConfirmDelete = async () => {
  if (taskToDelete.value) {
    await deleteTask(taskToDelete.value);
    isDeleteModalOpen.value = false;
    taskToDelete.value = null;
  }
};
</script>
