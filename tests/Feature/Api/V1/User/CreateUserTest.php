<?php

namespace Tests\Feature\Api\V1\User;

use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_can_create_user()
    {
        // Сначала наполняем базу ролями
        $this->seed(RolesAndAdminSeeder::class);

        // Создаем админа, от имени которого будем выполнять действие
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('admin');

        // Выполняем запрос на создание нового пользователя
        $response = $this->actingAs($admin)->postJson('/api/v1/users', [
            'name' => 'New Guy',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ]);

        // Проверяем результат
        $response->assertStatus(201); // 201 Created
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }
}
