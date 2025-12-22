<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Enums\Tasks\TaskPriority;
use App\Enums\Tasks\TaskStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class TaskMetaController extends Controller
{
    public function index(): JsonResponse
    {
        $statuses = array_map(function (TaskStatus $status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        }, TaskStatus::cases());

        $priorities = array_map(function (TaskPriority $priority) {
            return [
                'value' => $priority->value,
                'label' => $priority->label(),
            ];
        }, TaskPriority::cases());

        return response()->json([
            'statuses' => $statuses,
            'priorities' => $priorities,
        ]);
    }
}
