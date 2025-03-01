<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskBrief;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskBrief>
 */
class TaskBriefFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'outline' => fake()->name(),
            'note' => fake()->name(),
        ];
    }
}
