<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * @param  array{keyword?: string, status?: string, per_page?: int}  $filters
     */
    public function getAll(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()->with('user')->latest();

        $this->applyUserScope($query, $user);
        $this->keywordFilter($query, $filters['keyword'] ?? null);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(
            $this->getPerPage($filters)
        );
    }

    /**
     * @param  array{keyword?: string, per_page?: int}  $filters
     */
    public function getAllTrashed(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Task::query()
            ->with('user')
            ->onlyTrashed()
            ->latest();

        $this->applyUserScope($query, $user);
        $this->keywordFilter($query, $filters['keyword'] ?? null);

        return $query->paginate(
            $this->getPerPage($filters)
        );
    }

    public function findById(User $user, string $id): Task
    {
        $query = Task::query()->with('user');

        $this->applyUserScope($query, $user);

        return $query->findOrFail($id);
    }

    public function findDeletedById(User $user, string $id): Task
    {
        $query = Task::query()
            ->with('user')
            ->onlyTrashed();

        $this->applyUserScope($query, $user);

        return $query->findOrFail($id);
    }

    public function create(User $user, array $data): Task
    {
        $status = isset($data['status'])
            ? TaskStatus::from($data['status'])
            : TaskStatus::TODO;

        return $user->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => $status,
            'completed_at' => $status === TaskStatus::DONE
                ? now()
                : null,
        ])->load('user');
    }

    public function update(User $user, string $id, array $data): Task
    {
        $task = $this->findById($user, $id);

        if (array_key_exists('status', $data)) {
            $newStatus = TaskStatus::from($data['status']);

            $data['status'] = $newStatus;
            $data['completed_at'] = $newStatus === TaskStatus::DONE
                ? ($task->completed_at ?? now())
                : null;
        }

        $task->update($data);

        return $task->refresh()->load('user');
    }

    public function complete(User $user, string $id): Task
    {
        $task = $this->findById($user, $id);

        $task->update([
            'status' => TaskStatus::DONE,
            'completed_at' => $task->completed_at ?? now(),
        ]);

        return $task->refresh()->load('user');
    }

    public function restore(User $user, string $id): Task
    {
        $task = $this->findDeletedById($user, $id);

        $task->restore();

        return $task->refresh()->load('user');
    }

    public function delete(User $user, string $id): void
    {
        $this->findById($user, $id)->delete();
    }

    public function forceDelete(User $user, string $id): void
    {
        $this->findDeletedById($user, $id)->forceDelete();
    }

    private function applyUserScope(Builder $query, User $user): void
    {
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
    }

    private function keywordFilter(Builder $query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $keyword = trim($keyword);
        $query->where(function (Builder $query) use ($keyword) {
            $query->where('title', 'ILIKE', "%{$keyword}%")
                ->orWhere('description', 'ILIKE', "%{$keyword}%");
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
