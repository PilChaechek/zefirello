<?php

namespace Tests\Feature\Api\V1\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_create_user()
    {
        $admin = \App\Models\User::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/users', [
            'name' => 'New Guy',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201); // 201 Created
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }
}
