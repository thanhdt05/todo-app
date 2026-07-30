export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface PaginationLinks {
  first: string;
  last: string;
  prev: string;
  next: string;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string | null;
  data: T;
}

export interface PaginatedApiResponse<T> {
  success: boolean;
  message: string | null;
  data: T[];
  meta: PaginationMeta;
  links: PaginationLinks;
}
