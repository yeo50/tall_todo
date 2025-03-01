<?php

namespace Database\Factories;

use App\Models\Catalogue;
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
        return [
            'user_id' => User::factory(),
            'catalogue_id' => Catalogue::factory(),
            'name' => fake()->name(),
            'due' => fake()->date('Y-m-d'),
            'reminder' => fake()->dateTime(),
            'important' => rand(0, 1)
        ];
    }
}
