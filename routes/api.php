<?php

use App\Http\Controllers\Api\V1\Project\ProjectController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;

Route::prefix('v1')->group(function () {

    // Auth Domain
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes (Только для авторизованных)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Domain
        Route::get('/user', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // User Domain
        // Генерирует 5 маршрутов: index, store, show, update, destroy
        Route::apiResource('users', UserController::class);

        // Project Domain
        // Генерирует 5 маршрутов: index, store, show, update, destroy для ресурса проектов
        Route::apiResource('projects', ProjectController::class)->parameters(['projects' => 'project:slug']);
        // Добавление пользователя в проект
        Route::post('projects/{project:slug}/users', [ProjectController::class, 'addUser']);
        // Удаление пользователя из проекта
        Route::delete('projects/{project:slug}/users/{user}', [ProjectController::class, 'removeUser']);

        // Task Domain (Вложенный в проекты)
        Route::apiResource('projects.tasks', TaskController::class);
    });

});
