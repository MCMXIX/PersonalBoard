<?php

namespace App\Http\Controllers;

use App\Services\BoardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BoardController extends Controller
{
    public function __construct(private BoardService $boardService)
    {
    }

    /**
     * Get list of available boards/projects
     * @return JsonResponse
     */
    public function getBoardList() : JsonResponse
    {
        return $this->boardService->getBoards();
    }

    /**
     * Create new board
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        $params = $request->validate(['name' => 'required|string:255']);
        return $this->boardService->createBoard(Arr::get($params, 'name'));
    }

    /**
     * Update board name
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request) : JsonResponse
    {
        $params = $request->validate(['board_id' => 'required|int', 'name' => 'required|string:255']);
        return $this->boardService->updateBoard($params);
    }

    /**
     * Delete board
     * @param int $board_id
     * @return JsonResponse
     */
    public function delete(int $board_id) : JsonResponse
    {
        return $this->boardService->deleteBoard($board_id);
    }
}