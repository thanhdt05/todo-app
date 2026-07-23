<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request)
    {        
        $tasks = $this->taskService->getAll($request->user(), [
            'q' => $request->input('q'),
            'status' => $request->input('status')
        ]);

        return response()->json([
            'success' => true,
            "message" => "Lấy danh sách thành công",
            "data" => $tasks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->taskService->create($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            "message" => "Lưu thành công",
            "data" => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('view', $task);

        return response()->json([
            'success' => true,
            "message" => "Lấy thông tin thành công",
            "data" => $task
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('update', $task);

        $updatedTask = $this->taskService->update($request->user(), $id, $request->validated());

        return response()->json([
            'success' => true,
            "message" => "Cập nhật thành công",
            "data" => $updatedTask
        ], 200);
    }

    public function restore(Request $request, string $id) {
        $task = $this->taskService->findDeletedById($request->user(), $id);
        $this->authorize('restore', $task);

        $restoredTask = $this->taskService->restore($request->user(), $id);

        return response()->json([
            'success' => true,
            "message" => "Khôi phục thành công",
            "data" => $restoredTask
        ], 200);
    }

    public function complete(UpdateTaskRequest $request, string $id) {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('update', $task);

        $completedTask = $this->taskService->complete($request->user(), $id);

        return response()->json([
            'success' => true,
            "message" => "Cập nhật trạng thái thành công",
            "data" => $completedTask
        ], 200);
    }

    public function delete(Request $request, string $id) {
        $task = $this->taskService->findById($request->user(), $id);
        $this->authorize('delete', $task);

        $this->taskService->delete($request->user(), $id);

        return response()->json([
            'success' => true,
            "message" => "Xóa thành công",
            "data" => null
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $task = $this->taskService->findDeletedById($request->user(), $id);
        $this->authorize('delete', $task);

        $this->taskService->forceDelete($request->user(), $id);

        return response()->json([
            'success' => true,
            "message" => "Xóa thành công",
            "data" => null
        ], 200);
    }
}
