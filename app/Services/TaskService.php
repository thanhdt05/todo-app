<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class TaskService
{
    public function getAll(User $user, array $filters)
    {
        $query = $user->tasks()->latest();

        if (! empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $keyword = $filters['q'];

            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%");
            });
        }

        return $query->paginate(20);
    }

    public function findById(User $user, string $id)
    {
        return $user->tasks()->findOrFail($id);
    }

    public function findDeletedById(User $user, string $id)
    {
        return $user->tasks()->onlyTrashed()->findOrFail($id);
    }

    public function create(User $user, array $data): Task
    {
        $task = Task::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'due_date' => $data['due_date'],
        ]);

        return $task;
    }

    public function update(User $user, string $id, array $data)
    {
        $task = $this->findById($user, $id);

        $task->update($data);

        return $task;
    }

    public function restore(User $user, string $id)
    {
        $task = $this->findDeletedById($user, $id);

        $task->restore();

        return $task;
    }

    public function complete(User $user, string $id)
    {
        $task = $this->findById($user, $id);

        $task->update([
            'status' => 'done',
            'completed_at' => now(),
            'is_overdue' => $task->due_date < now(),
        ]);

        return $task;
    }

    public function delete(User $user, string $id)
    {
        $task = $this->findById($user, $id);

        $task->delete();
    }

    public function forceDelete(User $user, string $id)
    {
        $task = $this->findDeletedById($user, $id);

        $task->forceDelete();
    }
}
