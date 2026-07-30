export type TaskStatus = 'todo' | 'doing' | 'done';
export type TaskPriority = 'low' | 'medium' | 'high';
export type TaskTab = 'ALL' | 'TODO' | 'DOING' | 'DONE';

export interface Task {
  id: number;
  title: string;
  description: string;
  status: TaskStatus;
  priority: TaskPriority;
  due_date: string | null;
  completed_at: string | null;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  is_overdue?: boolean;
}

export interface TaskPayload {
  title: string;
  description?: string;
  due_date?: string | null;
  status?: TaskStatus;
  priority?: TaskPriority;
}

export interface TaskFilters {
  page?: number;
  per_page?: number;
  keyword?: string;
  status?: string;
  priority?: string;
  sort?: string;
  direction?: 'asc' | 'desc';
}
