<?php

use App\Http\Controllers\CatalogueController;
use App\Models\Catalogue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome');

Route::get('/test', function () {
    dd(Carbon::now()->format('Y-m-d'));
});

Route::get('/dashboard', function () {
    $catalogue = Catalogue::first();
    return view('catalogues.show', ['catalogue' => $catalogue]);
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('catalogues', CatalogueController::class)->middleware(['auth', 'verified']);

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
