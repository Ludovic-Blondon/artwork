<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\HomeController;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('work', function () {
        return Inertia::render('Work', [
            'paginatedWorks' => WorkResource::collection(
                Work::with('artist')->paginate(30)
            ),
        ]);
    })->name('work');

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
