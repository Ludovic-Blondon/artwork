<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('work', [WorkController::class, 'index'])
        ->name('work.index');

    Route::get('works/create', [WorkController::class, 'create'])
        ->name('work.create');

    Route::post('works', [WorkController::class, 'store'])
        ->name('work.store');

    Route::get('works/{work}/edit', [WorkController::class, 'edit'])
        ->name('work.edit');

    Route::patch('works/{work}', [WorkController::class, 'update'])
        ->name('work.update');

    Route::delete('works/{work}', [WorkController::class, 'destroy'])
        ->name('work.destroy');

    Route::get('artist', [ArtistController::class, 'index'])
        ->name('artist.index');

    Route::get('artists/create', [ArtistController::class, 'create'])
        ->name('artist.create');

    Route::post('artists', [ArtistController::class, 'store'])
        ->name('artist.store');

    Route::get('artists/{artist}/edit', [ArtistController::class, 'edit'])
        ->name('artist.edit');

    Route::patch('artists/{artist}', [ArtistController::class, 'update'])
        ->name('artist.update');

    Route::delete('artists/{artist}', [ArtistController::class, 'destroy'])
        ->name('artist.destroy');
});

require __DIR__.'/settings.php';
