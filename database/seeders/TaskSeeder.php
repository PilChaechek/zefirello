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
        // Найти администратора по email из конфига
        $admin = User::where('email', config('services.admin.email'))->first();
        // Если админ не найден, ничего не делаем
        if (! $admin) {
            return;
        }

        // Создать 5 проектов
        $projects = Project::factory(5)->create([
            'creator_id' => $admin->id,
        ]);

        // Привязать админа к каждому проекту
        $projects->each(function ($project) use ($admin) {
            $project->users()->attach($admin->id);
        });

        // Создать 20 задач
        Task::factory(20)->create();

        // Запустить сидер вложений
        $this->call(AttachmentSeeder::class);
    }
}
