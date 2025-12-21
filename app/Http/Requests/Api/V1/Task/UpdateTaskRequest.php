<?php

namespace App\Http\Requests\Api\V1\Task;

use App\Enums\Tasks\TaskPriority;
use App\Enums\Tasks\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'priority' => ['sometimes', new Enum(TaskPriority::class)],
            'order' => ['sometimes', 'integer', 'min:0'],
            'time_spent' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
