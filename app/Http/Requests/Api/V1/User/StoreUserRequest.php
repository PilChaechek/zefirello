<?php

// app/Http/Requests/Api/V1/User/StoreUserRequest.php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Позже здесь добавим проверку прав (can create users)
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // unique:users,email -> проверить уникальность в таблице users
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                Password::defaults() // Стандартные правила безопасности Laravel (мин 8 символов и т.д.)
            ],
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
