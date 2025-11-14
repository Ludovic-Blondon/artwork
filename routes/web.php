<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('artwork', [ArtworkController::class, 'index'])
        ->name('artwork.index');

    Route::get('artworks/create', [ArtworkController::class, 'create'])
        ->name('artwork.create');

    Route::post('artworks', [ArtworkController::class, 'store'])
        ->name('artwork.store');

    Route::get('artworks/{artwork}/edit', [ArtworkController::class, 'edit'])
        ->name('artwork.edit');

    Route::patch('artworks/{artwork}', [ArtworkController::class, 'update'])
        ->name('artwork.update');

    Route::delete('artworks/{artwork}', [ArtworkController::class, 'destroy'])
        ->name('artwork.destroy');

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

Route::get('artworks/{artwork}', [ArtworkController::class, 'show'])
    ->name('artwork.show');

require __DIR__.'/settings.php';
