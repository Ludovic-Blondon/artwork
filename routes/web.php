<?php

use App\Http\Controllers\HomeController;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\WorkResource;
use App\Models\Artist;
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

    Route::get('artist', function () {
        return Inertia::render('Artist', [
            'paginatedArtists' => ArtistResource::collection(
                Artist::paginate(30)
            ),
        ]);
    })->name('artist');
});

require __DIR__.'/settings.php';
