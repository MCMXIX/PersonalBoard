<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->group(function() {
    Route::get('/{board_id}', [TaskController::class, 'getTasks'])->name('tasks.get');
    Route::post('/', [TaskController::class, 'create'])->name('tasks.store');
    Route::post('/update', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::delete('/{task_id}', [TaskController::class, 'delete'])->name('tasks.destroy');
});

Route::prefix('boards')->group(function() {
    Route::get('/', [BoardController::class, 'getBoardList'])->name('board.get');
    Route::post('/', [BoardController::class, 'create'])->name('board.create');
    Route::post('/update', [BoardController::class, 'update'])->name('board.update');
    Route::delete('/{board_id}', [BoardController::class, 'delete'])->name('board.delete');
});