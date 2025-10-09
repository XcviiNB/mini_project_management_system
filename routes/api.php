<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskCommentController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/users', UserController::class);

Route::apiResource('/projects', ProjectController::class);

Route::apiResource('/tasks', TaskController::class);

Route::apiResource('/task-comments', TaskCommentController::class);
