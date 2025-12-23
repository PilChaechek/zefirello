<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ru_RU');
        $name = $faker->realText(50);

        return [
            'name' => rtrim($name, '.'),
            'slug' => Str::slug($name),
            'description' => $faker->realText(200),
            'creator_id' => User::factory(),
        ];
    }
}
