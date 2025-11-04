<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Inertia\Inertia;

class ArtistController extends Controller
{
    public function index()
    {
        return Inertia::render('artist/Index', [
            'paginatedArtists' => ArtistResource::collection(
                Artist::paginate(30)
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('artist/Form');
    }

    public function store(StoreArtistRequest $request)
    {
        Artist::create($request->validated());

        return redirect()->route('artist.index')->with('success', 'Artist created successfully.');
    }

    public function edit(Artist $artist)
    {
        return Inertia::render('artist/Form', [
            'artist' => ArtistResource::make($artist)->resolve(),
        ]);
    }

    public function update(UpdateArtistRequest $request, Artist $artist)
    {
        $artist->update($request->validated());

        return redirect()->route('artist.index')->with('success', 'Artist updated successfully.');
    }

    public function destroy(Artist $artist)
    {
        $artist->delete();

        return redirect()->route('artist.index')->with('success', 'Artist deleted successfully.');
    }
}
