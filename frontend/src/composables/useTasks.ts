import { ref, watch, onMounted } from 'vue';
import { taskApi } from '@/services/taskApi';
import type { Task, TaskTab, TaskFilters, TaskPriority } from '@/types/tasks';

export function useTasks(isTrashedMode: boolean = false) {
  const tasks = ref<Task[]>([]);
  const keyword = ref('');
  const currentTab = ref<TaskTab>('ALL');
  const priorityFilter = ref<TaskPriority | ''>('');
  const sortBy = ref<'created_at' | 'due_date' | 'priority' | 'title'>('created_at');
  const sortDirection = ref<'asc' | 'desc'>('desc');
  const currentPage = ref(1);
  const lastPage = ref(1);
  const totalTasks = ref(0);
  const isLoading = ref(false);

  const errorMessage = ref('');
  const successMessage = ref('');

  let searchTimer: ReturnType<typeof setTimeout> | null = null;
  let successTimer: ReturnType<typeof setTimeout> | null = null;
  let activeAbortController: AbortController | null = null;

  const handleApiError = (error: any, defaultMessage: string) => {
    if (error.response?.data) {
      if (error.response.data.errors) {
        const errorList = Object.values(error.response.data.errors).flat() as string[];
        if (errorList.length > 0) {
          errorMessage.value = errorList.join('. ');
        } else {
          errorMessage.value = error.response.data.message || defaultMessage;
        }
      } else {
        errorMessage.value = error.response.data.message || defaultMessage;
      }
    } else {
      errorMessage.value = error.message || 'Không thể kết nối đến máy chủ API';
    }
  };

  const fetchTasks = async (page: number = 1) => {
    isLoading.value = true;
    errorMessage.value = '';

    if (activeAbortController) {
      activeAbortController.abort();
    }

    activeAbortController = new AbortController();
    const signal = activeAbortController.signal;

    const filters: TaskFilters = {
      page,
      per_page: 5,
      sort: sortBy.value,
      direction: sortDirection.value,
    };

    if (keyword.value.trim()) {
      filters.keyword = keyword.value.trim();
    }

    if (!isTrashedMode && currentTab.value !== 'ALL') {
      filters.status = currentTab.value.toLowerCase();
    }

    if (priorityFilter.value) {
      filters.priority = priorityFilter.value;
    }

    try {
      const response = isTrashedMode
        ? await taskApi.listTrashed(filters, signal)
        : await taskApi.list(filters, signal);

      if (response.data?.data) {
        tasks.value = response.data.data;
        currentPage.value = response.data.meta?.current_page || 1;
        lastPage.value = response.data.meta?.last_page || 1;
        totalTasks.value = response.data.meta?.total || 0;
      }
    } catch (error: any) {
      if (error.name !== 'CanceledError' && error.name !== 'AbortError') {
        handleApiError(error, 'Không thể tải danh sách công việc');
      }
    } finally {
      isLoading.value = false;
    }
  };

  watch(successMessage, (newVal) => {
    if (successTimer) clearTimeout(successTimer);
    if (newVal) {
      successTimer = setTimeout(() => {
        successMessage.value = '';
      }, 2000);
    }
  });

  watch([keyword, currentTab, priorityFilter, sortBy, sortDirection], () => {
    if (searchTimer) clearTimeout(searchTimer);

    searchTimer = setTimeout(() => {
      fetchTasks(1);
    }, 300);
  });

  const gotoPage = (page: number) => {
    fetchTasks(page);
  };

  const toggleComplete = async (task: Task) => {
    if (task.status === 'done') return;
    try {
      const response = await taskApi.complete(task.id);

      successMessage.value = response.data.message || 'Đã hoàn thành công việc';
      await fetchTasks();
    } catch (error: any) {
      handleApiError(error, 'Cập nhật công việc thất bại');
    }
  };

  const deleteTask = async (task: Task) => {
    try {
      const response = await taskApi.delete(task.id);

      successMessage.value = response.data.message || 'Đã xóa công việc';
      await fetchTasks();
    } catch (error: any) {
      handleApiError(error, 'Xóa công việc thất bại');
    }
  };

  const forceDeleteTask = async (task: Task) => {
    try {
      const response = await taskApi.forceDelete(task.id);

      successMessage.value = response.data.message || 'Đã xóa công việc';
      await fetchTasks();
    } catch (error: any) {
      handleApiError(error, 'Xóa công việc thất bại');
    }
  };

  const restoreTask = async (task: Task) => {
    try {
      const response = await taskApi.restore(task.id);

      successMessage.value = response.data.message || 'Đã khôi phục công việc';
      await fetchTasks();
    } catch (error: any) {
      handleApiError(error, 'Khôi phục công việc thất bại');
    }
  };

  onMounted(() => {
    fetchTasks();
  });

  return {
    tasks,
    keyword,
    currentTab,
    priorityFilter,
    sortBy,
    sortDirection,
    currentPage,
    lastPage,
    totalTasks,
    isLoading,
    errorMessage,
    successMessage,

    fetchTasks,
    gotoPage,
    toggleComplete,
    deleteTask,
    forceDeleteTask,
    restoreTask,
  };
}
