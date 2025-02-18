<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskGroupController;

Route::get('/', [IndexController::class, 'index'])->where("period", "3|7|30");
Route::get('/schedule/{dt}/{period}', [IndexController::class, 'schedule'])
        ->where("period", "3|7|30")
        ->where("dt", "\d{8}")->name('schedule');

Route::get('/reports', function () {
    return view('reports');
})->name("reports");

Route::get('/task-group', function () {
    return view('task-group');
})->name("task-group");

Route::get('/task-group/list', [TaskGroupController::class, 'getList'])->name("task-group.list");
Route::get('/task-group/edit/{taskGroup}', [TaskGroupController::class, 'editGet'])->name("task-group.edit");
Route::post('/task-group/add', [TaskGroupController::class, 'add'])->name("task-group.add");
Route::post('/task-group/edit/{taskGroup}', [TaskGroupController::class, 'editPost'])->name("task-group.edit");
Route::post('/task-group/delete/{taskGroup}', [TaskGroupController::class, 'delete'])->name("task-group.delete");

Route::get('/task/add', [TaskController::class, 'addGet'])->name("task.add");
Route::post('/task/add', [TaskController::class, 'addPost'])->name("task.add");
Route::post('/task/add-batch', [TaskController::class, 'addBatch'])->name("task.add-batch");
Route::get('/task/edit/{task}', [TaskController::class, 'editGet'])->name("task.edit");
Route::post('/task/edit/{task}', [TaskController::class, 'editPost'])->name("task.edit");
Route::post('/task/delete/{task}', [TaskController::class, 'delete'])->name("task.delete");

