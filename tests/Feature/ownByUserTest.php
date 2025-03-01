<?php

use App\Models\Catalogue;
use App\Models\Task;
use App\Models\TaskBrief;
use App\Models\User;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;


test('user only see their own catelogue', function () {
    $user = User::factory()->has(Catalogue::factory()->count(2))->create();
    $stranger = User::factory()->has(Catalogue::factory()->count(3))->create();
    $this->actingAs($user);

    $userCatalogues = Catalogue::all();

    expect($userCatalogues)->toHaveCount(2);
    $userCatalogues->each(fn($catalogue) => expect($catalogue->user_id)->toBe($user->id));
});


test('user only sees their own tasks with the right catalogue', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $this->actingAs($user);

    DB::table('catalogues')->insert([
        'user_id' => $user->id,
        'name' => 'work'
    ]);
    DB::table('catalogues')->insert([
        'user_id' => $stranger->id,
        'name' => 'not work'
    ]);
    $catalogue = Catalogue::first();
    $strangerCatalogue = Catalogue::withoutGlobalScopes()->where('user_id', $stranger->id)->first();

    $userInsertedTasks = [];
    for ($i = 0; $i < 3; $i++) {
        $userInsertedTasks[] = [
            'user_id' => $user->id,
            'catalogue_id' => $catalogue->id,
            'name' => fake()->name(),
            'important' => rand(0, 1)
        ];
    }
    $strangerInsertedTasks = [];
    for ($i = 0; $i < 5; $i++) {
        $strangerInsertedTasks[] = [
            'user_id' => $stranger->id,
            'catalogue_id' => $strangerCatalogue->id,
            'name' => fake()->name(),
            'important' => rand(0, 1)
        ];
    }
    DB::table('tasks')->insert($userInsertedTasks);
    DB::table('tasks')->insert($strangerInsertedTasks);
    $userTasks = Task::all();
    expect($userTasks)->toHaveCount(3);
    $userTasks->each(fn($task) => expect([$task->user_id, $task->catalogue_id])->toBe([$user->id, $catalogue->id]));
});


test('task only see their respective outlines and notes', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $this->actingAs($user);

    DB::table('catalogues')->insert([
        'user_id' => $user->id,
        'name' => 'work'
    ]);
    $catalogue = Catalogue::first();
    DB::table('tasks')->insert(
        [
            'user_id' => $user->id,
            'catalogue_id' => $catalogue->id,
            'name' => fake()->name(),
            'important' => rand(0, 1)
        ]
    );
    $task = Task::first();
    DB::table('task_briefs')->insert([
        'task_id' => $task->id,
        'outline' => 'meeting',
        'note' => 'need to do'
    ]);
    $taskBriefs = TaskBrief::all();
    expect($taskBriefs)->toHaveCount(1);
    $taskBriefs->each(fn($brief) => expect($brief->task_id)->toBe($task->id));
});
