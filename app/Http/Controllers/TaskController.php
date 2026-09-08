<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\CreateTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Task Controller
     */
    public function __construct(private TaskService $taskManagementService) {}

    /**
     * Create new tasks
     */
    public function create(CreateTaskRequest $request): JsonResponse
    {
        return $this->taskManagementService->createTask($request->validated());
    }

    /**
     * Get available tasks for the board
     */
    public function getTasks(int $board_id): JsonResponse
    {
        return $this->taskManagementService->getTasksByBoardId($board_id);
    }

    /**
     * Update task details
     */
    public function update(UpdateTaskRequest $request): JsonResponse
    {
        return $this->taskManagementService->updateTask($request->validated());
    }

    /**
     * Remove task in the board
     */
    public function delete(int $task_id): JsonResponse
    {
        return $this->taskManagementService->deleteTask($task_id);
    }

    /**
     * Reorder tasks in the board
     */
    public function reorder(Request $request): JsonResponse
    {
        $params = $request->validate([
            '*.id'       => ['required', 'integer', 'distinct', 'exists:tasks,id'],
            '*.priority' => ['required', 'integer'],
        ]);

        return $this->taskManagementService->reorderTasks($params);
    }
}
