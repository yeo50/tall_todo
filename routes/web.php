<?php

use App\Http\Controllers\CatalogueController;
use App\Models\Catalogue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome');

Route::get('/dashboard', function () {
    $catalogue = Catalogue::first();
    return redirect()->route('catalogues.show', $catalogue->id)->with('message', 'testing session message');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::resource('catalogues', CatalogueController::class);
Route::view('work', 'work')->name('work');
Route::view('important', 'important')->name('important');
Route::view('routine', 'routine')->name('routine');
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
