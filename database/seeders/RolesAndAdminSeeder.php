<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        // 1. Получаем данные из конфига
        $adminEmail = config('services.admin.email');
        $adminPassword = config('services.admin.password');

        // 2. ЗАЩИТА: Если пароль не задан, останавливаем сидинг с ошибкой
        if (empty($adminPassword)) {
            throw new \Exception('⚠️ ОШИБКА: Не задан ADMIN_PASSWORD в .env файле!');
        }

        // 3. Создаем Супер Админа
        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($adminPassword)
            ]
        );
        $admin->syncRoles($roleAdmin);

    }
}
