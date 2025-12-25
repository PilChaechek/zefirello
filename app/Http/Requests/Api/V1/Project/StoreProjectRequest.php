<?php

namespace App\Http\Requests\Api\V1\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:projects,slug'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer', 'exists:users,id'],
        ];
    }
}
