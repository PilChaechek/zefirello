<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Убедимся, что у нас есть хотя бы один проект и пользователь
        if (Project::count() === 0) {
            Project::factory()->create();
        }
        if (User::count() === 0) {
            User::factory()->create();
        }

        Task::factory(20)->create();
    }
}
