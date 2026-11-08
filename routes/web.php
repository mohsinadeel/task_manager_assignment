<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::scopeBindings()->prefix('projects/{project}')->group(function () {
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
});
