<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/test', function () {
    $now = now();

    $date = $now->format('Y-m-d');
    dd($date . ' ' . '23:00');
});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::view('work', 'work')->name('work');
Route::view('important', 'important')->name('important');
Route::view('routine', 'routine')->name('routine');
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
