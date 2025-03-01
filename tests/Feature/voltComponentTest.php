<?php

use App\Models\Catalogue;
use App\Models\Task;
use App\Models\TaskBrief;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Volt;


test('catalogue-menu can render', function () {

    $user = User::factory()->create();
    $this->actingAs($user);

    DB::table('catalogues')->insert([
        'user_id' => $user->id,
        'name' => 'work'
    ]);
    DB::table('catalogues')->insert([
        'user_id' => $user->id,
        'name' => 'routine'
    ]);
    $component = Volt::test('partials.catalogue-menu');
    $component->assertViewHas('catalogues', function ($catalogues) {
        return  count($catalogues) == 2;
    });

    $component->set('name', 'important');
    $component->call('addCatalogue');
    $component->assertViewHas('catalogues', function ($catalogues) {
        return  count($catalogues) == 3;
    });
    $latestCatalogue = Catalogue::latest()->first();

    expect($latestCatalogue->name)->toBe('important');
});
