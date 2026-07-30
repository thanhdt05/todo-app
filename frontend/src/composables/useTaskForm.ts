import { ref, onMounted, computed } from 'vue';
import { taskApi } from '@/services/taskApi';
import type { Task, TaskPayload } from '@/types/tasks';

export function useTaskForm(onSuccessCallback?: () => void) {
  const isModalOpen = ref(false);
  const isEditMode = ref(false);
  const isSubmitting = ref(false);
  const selectedTaskId = ref<number | null>(null);
  const errorMessage = ref('');

  const taskForm = ref<TaskPayload>({
    title: '',
    description: '',
    due_date: '',
    status: 'todo',
    priority: 'medium',
  });

  const formatDate = (dateStr: string | null): string => {
    if (!dateStr) return '';
    return dateStr.split('T')[0] ?? '';
  };

  const modalTitle = computed(() => {
    if (!selectedTaskId.value) {
      return 'Thêm công việc';
    }

    return isEditMode.value ? 'Sửa công việc' : 'Xem công việc';
  });

  const openCreateModal = () => {
    errorMessage.value = '';
    isEditMode.value = true;
    taskForm.value = {
      title: '',
      description: '',
      due_date: '',
      status: 'todo',
      priority: 'medium',
    };
    isModalOpen.value = true;
  };

  const openEditModal = (task: Task) => {
    errorMessage.value = '';
    isEditMode.value = true;
    selectedTaskId.value = task.id;
    taskForm.value = {
      title: task.title,
      description: task.description || '',
      due_date: formatDate(task.due_date),
      status: task.status,
      priority: task.priority || 'medium',
    };
    isModalOpen.value = true;
  };

  const openDetailModal = (task: Task) => {
    errorMessage.value = '';
    isEditMode.value = false;
    selectedTaskId.value = task.id;
    taskForm.value = {
      title: task.title,
      description: task.description || '',
      due_date: formatDate(task.due_date),
      status: task.status,
      priority: task.priority || 'medium',
    };
    isModalOpen.value = true;
  };

  const closeModal = () => {
    errorMessage.value = '';
    isModalOpen.value = false;
    selectedTaskId.value = null;
  };

  const submitForm = async () => {
    errorMessage.value = '';
    isSubmitting.value = true;

    try {
      if (selectedTaskId.value) {
        await taskApi.update(selectedTaskId.value, taskForm.value);
      } else {
        await taskApi.create(taskForm.value);
      }
      closeModal();

      if (onSuccessCallback) {
        onSuccessCallback();
      }
    } catch (error: any) {
      if (error.response?.data?.errors) {
        const errorList = Object.values(error.response.data.errors).flat() as string[];
        errorMessage.value = errorList.join('. ');
      } else {
        errorMessage.value = error.response?.data?.message || 'Thao tác thất bại';
      }
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    isModalOpen,
    isEditMode,
    isSubmitting,
    selectedTaskId,
    taskForm,
    modalTitle,
    errorMessage,

    openCreateModal,
    openDetailModal,
    openEditModal,
    closeModal,
    formatDate,
    submitForm,
  };
}
