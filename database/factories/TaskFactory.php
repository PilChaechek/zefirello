<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ru_RU');

        $statuses = ['todo', 'in_progress', 'done', 'canceled'];
        $priorities = ['low', 'medium', 'high'];

        $project = Project::inRandomOrder()->first();
        $creator = User::inRandomOrder()->first();
        $assignee = User::inRandomOrder()->first();

        return [
            'title' => $faker->realText(50),
            'description' => $faker->realText(200),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'order' => $this->faker->numberBetween(0, 100),
            'time_spent' => $this->faker->numberBetween(0, 480), // 0-8 часов в минутах
            'project_id' => $project ? $project->id : Project::factory(),
            'assignee_id' => $assignee ? $assignee->id : null,
            'creator_id' => $creator ? $creator->id : User::factory(),
            'due_date' => $this->faker->boolean(50) ? $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d') : null,
        ];
    }
}
