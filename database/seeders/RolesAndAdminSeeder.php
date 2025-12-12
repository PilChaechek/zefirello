<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Сброс кеша ролей
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Создаем роли (firstOrCreate - если есть, не создаст дубль)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        // 2. Создаем Супер Админа
        $admin = User::firstOrCreate(
            ['email' => 'admin@zefirello.ru'], // Поиск по Email
            [
                'name' => 'Super Admin',
                'password' => bcrypt('nvG#7#^?zA}')
            ]
        );
        // Назначаем роль (syncRoles безопаснее, чем assignRole при повторном запуске)
        $admin->syncRoles($roleAdmin);

        // 3. Создаем Тестового юзера
        $user = User::firstOrCreate(
            ['email' => 'user@zefirello.ru'],
            [
                'name' => 'Test User',
                'password' => bcrypt('7#^?zA}vGD&'),
            ]
        );
        $user->syncRoles($roleUser);
    }
}
