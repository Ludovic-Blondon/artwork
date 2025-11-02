<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Inertia\Inertia;

class ArtistController extends Controller
{
    public function index()
    {
        return Inertia::render('Artist', [
            'paginatedArtists' => ArtistResource::collection(
                Artist::paginate(30)
            ),
        ]);
    }

    public function destroy(Artist $artist)
    {
        $artist->delete();

        return redirect()->route('artist.index')->with('success', 'Artist deleted successfully.');
    }
}
