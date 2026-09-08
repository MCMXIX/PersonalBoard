<?php

namespace App\Services;

use App\Models\BoardModel;
use App\Models\TaskModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * @var BoardModel
     */
    private $boardModel;

    /**
     * TaskService Constructor.
     */
    public function __construct(private TaskModel $taskModel)
    {
        $this->boardModel = (new BoardModel);
    }

    /**
     * Get available tasks by board_id
     */
    public function getTasksByBoardId(int $boardId): JsonResponse
    {
        $taskList = $this->taskModel->where(['board_id' => $boardId])
            ->orderBy('priority')
            ->orderBy('id')
            ->cursorPaginate(10);

        return response()->json([
            'tasks' => $taskList,
        ], 200);
    }

    /**
     * Create new task for the selected board
     */
    public function createTask(array $task): JsonResponse
    {
        $board = $this->boardModel->find($task['board_id']);
        if ($board) {
            $priority = $this->taskModel->where('board_id', $task['board_id'])
                ->max('priority');

            $result = $this->taskModel->create(array_merge(Arr::only($task, [
                'board_id',
                'name',
                'type',
            ]), ['priority' => ($priority + 1)]));
            if (! $result) {
                return response()->json([
                    'message' => 'Something went wrong with the request.',
                ], 500);
            }

            return response()->json([
                'message' => 'Task successfully created.',
            ], 200);
        }

        return response()->json([
            'message' => 'Project not found.',
        ], 404);
    }

    /**
     * Update task details
     */
    public function updateTask(array $params): JsonResponse
    {
        $taskId = Arr::pull($params, 'task_id');
        $task = $this->taskModel->find($taskId);
        if ($task) {
            $task->name = Arr::get($params, 'name');
            $task->type = Arr::get($params, 'type');
            $result = $task->save();
            if (!$result) {
                return response()->json([
                    'message' => 'Something went wrong updating the task.',
                ], 500);
            }

            return response()->json([
                'message' => 'Task successfully updated.',
            ], 200);
        }

        return response()->json([
            'message' => 'Project not found.',
        ], 404);
    }

    /**
     * Delete task from the board
     */
    public function deleteTask(int $taskId): JsonResponse
    {
        $deleted = $this->taskModel->where('id', $taskId)->delete();
        if (!$deleted) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function reorderTasks(array $tasks)
    {
        DB::transaction(function () use ($tasks): void {
            foreach ($tasks as $task) {
                $taskModel = $this->taskModel->newQuery()
                    ->lockForUpdate()
                    ->findOrFail($task['id']);

                $taskModel->update([
                    'priority' => $task['priority'],
                ]);
            }
        });

        return response()->json([
            'message' => 'Tasks successfully reordered.',
        ], 200);
    }
}
