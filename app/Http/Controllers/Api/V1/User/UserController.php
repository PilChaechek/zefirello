<?php

// app/Http/Controllers/Api/V1/User/UserController.php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\StoreUserRequest;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('roles')->latest()->get(); // Изменено: убрана пагинация

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $this->authorize('create', User::class);
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

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);
        $data = $request->validated();

        // Если пароль передан и он не пустой, хешируем его
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Иначе - убираем его из массива, чтобы не перезаписать на пустую строку
            unset($data['password']);
        }

        $user->update($data);

        // Синхронизируем роль
        if ($request->has('role')) {
            // syncRoles гарантирует, что у пользователя будет только эта одна роль
            $user->syncRoles([$request->role]);
        }

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->noContent();
    }
}
