<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Enums\Tasks\TaskPriority;
use App\Enums\Tasks\TaskStatus;
use App\Http\Controllers\Controller;

class TaskMetaController extends Controller
{
    public function index()
    {
        return response()->json([
            'statuses' => array_map(fn($enum) => $enum->value, TaskStatus::cases()),
            'priorities' => array_map(fn($enum) => $enum->value, TaskPriority::cases()),
        ]);
    }
}
