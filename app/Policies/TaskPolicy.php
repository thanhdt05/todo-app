<?php

namespace App\Policies;

use App\Enums\TaskPermission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->allowAnyPermission(
            $user,
            [
                TaskPermission::VIEW_ALL,
                TaskPermission::VIEW_OWN,
            ],
            'Bạn không có quyền xem danh sách công việc'
        );
    }

    /**
     * Determine whether the user can view any trashed models.
     */
    public function viewTrash(User $user): Response
    {
        return $this->allowAnyPermission(
            $user,
            [
                TaskPermission::VIEW_TRASHED_ALL,
                TaskPermission::VIEW_TRASHED_OWN,
            ],
            'Bạn không có quyền xem danh sách công việc đã xóa'
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): Response
    {
        return $this->allowOwnOrAll(
            $user,
            $task,
            allPermission: TaskPermission::VIEW_ALL,
            ownPermission: TaskPermission::VIEW_OWN,
            message: 'Bạn không có quyền xem công việc này'
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can(TaskPermission::CREATE)
            ? Response::allow()
            : Response::deny('Bạn không có quyền tạo công việc');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        return $this->allowOwnOrAll(
            $user,
            $task,
            allPermission: TaskPermission::UPDATE_ALL,
            ownPermission: TaskPermission::UPDATE_OWN,
            message: 'Bạn không có quyền cập nhật công việc này'
        );
    }

    /**
     * Determine whether the user can complete the model.
     */
    public function complete(User $user, Task $task): Response
    {
        return $this->allowOwnOrAll(
            $user,
            $task,
            allPermission: TaskPermission::COMPLETE_ALL,
            ownPermission: TaskPermission::COMPLETE_OWN,
            message: 'Bạn không có quyền hoàn thành công việc này'
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return $this->allowOwnOrAll(
            $user,
            $task,
            allPermission: TaskPermission::DELETE_ALL,
            ownPermission: TaskPermission::DELETE_OWN,
            message: 'Bạn không có quyền xóa công việc này'
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): Response
    {
        return $this->allowOwnOrAll(
            $user,
            $task,
            allPermission: TaskPermission::RESTORE_ALL,
            ownPermission: TaskPermission::RESTORE_OWN,
            message: 'Bạn không có quyền khôi phục công việc này'
        );
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): Response
    {
        return $this->allowAnyPermission(
            $user,
            [
                TaskPermission::RESTORE_ALL,
                TaskPermission::RESTORE_OWN,
            ],
            'Bạn không có quyền khôi phục công việc'
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): Response
    {
        return $user->can(TaskPermission::FORCE_DELETE_ALL)
            ? Response::allow()
            : Response::deny('Bạn không có quyền xóa vĩnh viễn công việc này');
    }

    /**
     * Determine whether the user has any of the given permissions.
     *
     * @param  array<int, TaskPermission|string>  $permissions
     */
    private function allowAnyPermission(
        User $user,
        array $permissions,
        string $message
    ): Response {
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return Response::allow();
            }
        }

        return Response::deny($message);
    }

    /**
     * Determine whether the user has "all" permission or owns the task with "own" permission.
     */
    private function allowOwnOrAll(
        User $user,
        Task $task,
        TaskPermission|string $allPermission,
        TaskPermission|string $ownPermission,
        string $message
    ): Response {
        if ($user->can($allPermission)) {
            return Response::allow();
        }

        if ($user->can($ownPermission) && $user->getKey() === $task->getAttribute('user_id')) {
            return Response::allow();
        }

        return Response::deny($message);
    }
}
