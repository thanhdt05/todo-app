<?php

namespace App\Services;

use App\Enums\TaskPermission;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * @param  array{keyword?: string, status?: string, priority?: string, sort?: string, direction?: string, per_page?: int}  $filters
     */
    public function getAll(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()->with('user');

        $this->applyVisibilityScope(
            query: $query,
            user: $user,
            viewAllPermission: TaskPermission::VIEW_ALL
        );
        $this->keywordFilter($query, $filters['keyword'] ?? null);
        $this->applyFiltersAndSorting($query, $filters);

        return $query->paginate(
            $this->getPerPage($filters)
        );
    }

    /**
     * @param  array{keyword?: string, priority?: string, sort?: string, direction?: string, per_page?: int}  $filters
     */
    public function getAllTrashed(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()->with('user')->onlyTrashed();

        $this->applyVisibilityScope(
            query: $query,
            user: $user,
            viewAllPermission: TaskPermission::VIEW_TRASHED_ALL
        );
        $this->keywordFilter($query, $filters['keyword'] ?? null);
        $this->applyFiltersAndSorting($query, $filters);

        return $query->paginate(
            $this->getPerPage($filters)
        );
    }

    public function findById(string $id): Task
    {
        return Task::query()
            ->with('user')
            ->findOrFail($id);
    }

    public function findDeletedById(string $id): Task
    {
        return Task::query()
            ->with('user')
            ->onlyTrashed()
            ->findOrFail($id);
    }

    public function findDeletedByIds(array $ids): Collection
    {
        return Task::query()
            ->with('user')
            ->onlyTrashed()
            ->whereKey($ids)
            ->get();
    }

    public function create(User $user, array $data): Task
    {
        $status = isset($data['status'])
            ? TaskStatus::from($data['status'])
            : TaskStatus::TODO;

        $priority = isset($data['priority'])
            ? TaskPriority::from($data['priority'])
            : TaskPriority::MEDIUM;

        return $user->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => $status,
            'priority' => $priority,
            'completed_at' => $status === TaskStatus::DONE ? now() : null,
        ])->load('user');
    }

    public function update(Task $task, array $data): Task
    {
        if (array_key_exists('status', $data)) {
            $newStatus = TaskStatus::from($data['status']);

            $data['status'] = $newStatus;
            $data['completed_at'] = $newStatus === TaskStatus::DONE ? ($task->completed_at ?? now()) : null;
        }

        if (array_key_exists('priority', $data)) {
            $data['priority'] = TaskPriority::from($data['priority']);
        }

        if (array_key_exists('due_date', $data)) {
            $newDueDate = $data['due_date'];
            $oldDueDate = $task->due_date?->toDateTimeString();

            if ($newDueDate !== $oldDueDate) {
                $data['reminder_sent_at'] = null;
            }
        }

        $task->update($data);

        return $task->refresh()->load('user');
    }

    public function complete(Task $task): Task
    {
        $task->update([
            'status' => TaskStatus::DONE,
            'completed_at' => $task->completed_at ?? now(),
        ]);

        return $task->refresh()->load('user');
    }

    public function restore(Task $task): Task
    {
        $task->restore();

        return $task->refresh()->load('user');
    }

    public function bulkRestore(Collection $tasks): int
    {
        if ($tasks->isEmpty()) {
            return 0;
        }

        return Task::onlyTrashed()
            ->whereKey($tasks->modelKeys())
            ->restore();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function forceDelete(Task $task): bool
    {
        return $task->forceDelete();
    }

    private function applyVisibilityScope(
        Builder $query,
        User $user,
        TaskPermission $viewAllPermission
    ): void {
        if ($user->cannot($viewAllPermission)) {
            $query->where('user_id', $user->getKey());
        }
    }

    private function applyFiltersAndSorting(Builder $query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = strtolower($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['due_date', 'created_at', 'priority', 'title'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest();
        }
    }

    private function keywordFilter(Builder $query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $keyword = mb_strtolower(trim($keyword));
        /** @var Connection $connection */
        $connection = $query->getConnection();
        $likeOperator = $connection->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        $query->where(function (Builder $query) use ($keyword, $likeOperator) {
            $query->where('title', $likeOperator, "%{$keyword}%")
                ->orWhere('description', $likeOperator, "%{$keyword}%");
        });
    }

    private function getPerPage(array $filters): int
    {
        return min(
            max((int) ($filters['per_page'] ?? 5), 1),
            100
        );
    }
}
