<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkRestoreTaskRequest;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\HttpResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests, HttpResponse;

    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(IndexTaskRequest $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getAll($request->user(), [
            'keyword' => $request->input('keyword'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'per_page' => $request->input('per_page'),
        ]);

        return $this->paginated(
            TaskResource::collection($tasks),
            'Lấy danh sách thành công'
        );
    }

    public function getAllTrashedTasks(IndexTaskRequest $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getAllTrashed($request->user(), [
            'keyword' => $request->input('keyword'),
            'priority' => $request->input('priority'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'per_page' => $request->input('per_page'),
        ]);

        return $this->paginated(
            TaskResource::collection($tasks),
            'Lấy danh sách đã xóa thành công'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->create($request->user(), $request->validated());

        return $this->success(
            TaskResource::make($task),
            'Thêm mới thành công',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('view', $task);

        return $this->success(
            TaskResource::make($task),
            'Lấy thông tin thành công'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('update', $task);

        $updatedTask = $this->taskService->update($task, $request->validated());

        return $this->success(
            TaskResource::make($updatedTask),
            'Cập nhật thành công'
        );
    }

    public function restore(Request $request, string $id)
    {
        $task = $this->taskService->findDeletedById($request->user(), $id);
        $this->authorize('restore', $task);

        $restoredTask = $this->taskService->restore($task);

        return $this->success(
            TaskResource::make($restoredTask),
            'Khôi phục thành công'
        );
    }

    public function bulkRestore(BulkRestoreTaskRequest $request)
    {
        $this->authorize('restoreAny', Task::class);

        $restoredCount = $this->taskService->bulkRestore($request->user(), $request->validated());

        return $this->success(
            ['restored_count' => $restoredCount],
            "Đã khôi phục thành công {$restoredCount} công việc"
        );
    }

    public function complete(Request $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('complete', $task);

        $completedTask = $this->taskService->complete($task);

        return $this->success(
            TaskResource::make($completedTask),
            'Cập nhật trạng thái thành công'
        );
    }

    public function delete(Request $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return $this->success(
            data : null,
            message: 'Xóa thành công'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $task = $this->taskService->findDeletedById($request->user(), $id);
        $this->authorize('forceDelete', $task);

        $this->taskService->forceDelete($task);

        return $this->success(
            data: null,
            message: 'Xóa thành công'
        );
    }
}
