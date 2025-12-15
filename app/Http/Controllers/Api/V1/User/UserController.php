<?php
// app/Http/Controllers/Api/V1/User/UserController.php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('roles')->latest()->paginate(10);
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        // Получаем только валидированные данные
        $data = $request->validated();

        // Хешируем пароль перед сохранением
        $data['password'] = Hash::make($data['password']);

        // Создаем (пока без Action класса, для простоты MVP)
        $user = User::create($data);

        // 2. Присваиваем роль
        if ($request->has('role')) {
            $user->assignRole($request->role);
        }

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
