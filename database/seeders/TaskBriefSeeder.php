<?php

namespace Database\Seeders;

use App\Models\TaskBrief;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskBriefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskBrief::factory(5)->create();
    }
}
