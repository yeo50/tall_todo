<?php

use App\Http\Controllers\CatalogueController;
use App\Models\Catalogue;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    } else {
        return view('welcome');
    }
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
