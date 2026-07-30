import type { AxiosResponse } from 'axios';
import api from './api';

import type { Task, TaskFilters, TaskPayload } from '@/types/tasks';
import type { ApiResponse, PaginatedApiResponse } from '@/types/api';

export const taskApi = {
  list(filters: TaskFilters = {}, signal?: AbortSignal): Promise<AxiosResponse<PaginatedApiResponse<Task>>> {
    return api.get('/tasks', { params: filters, signal });
  },

  listTrashed(filters: TaskFilters = {}, signal?: AbortSignal): Promise<AxiosResponse<PaginatedApiResponse<Task>>> {
    return api.get('/tasks/trashed', { params: filters, signal });
  },

  get(id: number): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.get(`/tasks/${id}`);
  },

  create(payload: TaskPayload): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.post('/tasks', payload);
  },

  update(id: number, payload: Partial<TaskPayload>): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.patch(`/tasks/${id}`, payload);
  },

  complete(id: number): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.put(`/tasks/${id}/complete`);
  },

  delete(id: number): Promise<AxiosResponse<ApiResponse<null>>> {
    return api.delete(`/tasks/${id}`);
  },

  forceDelete(id: number): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.delete(`/tasks/${id}/force`);
  },

  restore(id: number): Promise<AxiosResponse<ApiResponse<Task>>> {
    return api.put(`/tasks/${id}/restore`);
  },

  bulkRestore(ids: number[]): Promise<AxiosResponse<ApiResponse<{ restored_count: number }>>> {
    return api.put('/tasks/bulk-restore', { ids });
  },
};
