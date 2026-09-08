<?php

namespace App\Services;

use App\Models\BoardModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BoardService
{
    public function __construct(private BoardModel $boardModel)
    {
    }

    /**
     * Get list of available boards
     * @return JsonResponse
     */
    public function getBoards() : JsonResponse
    {
        $boards = $this->boardModel->all();
        return response()->json([
            'boards' => $boards
        ], 200);
    }

    /**
     * Create new board
     * @param string $name
     * @return JsonResponse
     */
    public function createBoard(string $name) : JsonResponse
    {
        $board = $this->boardModel->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        if (!$board) {
            return response()->json([
                'message' => 'Something went wrong creating new board.'
            ], 500);
        }

        return response()->json([
            'message' => 'Board successfully created.'
        ], 200);
    }

    /**
     * Update board name
     * @param array $params
     * @return JsonResponse
     */
    public function updateBoard(array $params) : JsonResponse
    {
        $board = $this->boardModel->find($params['board_id']);
        if ($board) {
            $board->name = Arr::get($params, 'name');
            $board->save;

            return response()->json([
                'message' => 'Project name successfully updated.'
            ], 200);
        }

        return response()->json([
            'message' => 'Project not found.'
        ], 404);
    }

    /**
     * Delete board
     * @param int $boardId
     * @return JsonResponse
     */
    public function deleteBoard(int $boardId) : JsonResponse
    {
        $deleted = $this->boardModel->where('id', $boardId)->delete();
        if (!$deleted) {
            return response()->json([
                'message' => 'Project not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Project deleted successfully.'
        ]);
    }
}