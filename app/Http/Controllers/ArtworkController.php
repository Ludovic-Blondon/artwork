<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtworkRequest;
use App\Http\Requests\UpdateArtworkRequest;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\ArtworkResource;
use App\Models\Artist;
use App\Models\Artwork;
use Inertia\Inertia;

class ArtworkController extends Controller
{
    public function index()
    {
        return Inertia::render('artwork/Index', [
            'paginatedArtworks' => ArtworkResource::collection(
                Artwork::with('artist')->paginate(30)
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('artwork/Form', [
            'artists' => ArtistResource::collection(Artist::all())->resolve(),
        ]);
    }

    public function store(StoreArtworkRequest $request)
    {
        Artwork::create($request->validated());

        return redirect()->route('artwork.index')->with('success', 'Artwork created successfully.');
    }

    public function edit(Artwork $artwork)
    {
        return Inertia::render('artwork/Form', [
            'artwork' => ArtworkResource::make($artwork->load('artist'))->resolve(),
            'artists' => ArtistResource::collection(Artist::all())->resolve(),
        ]);
    }

    public function update(UpdateArtworkRequest $request, Artwork $artwork)
    {
        $artwork->update($request->validated());

        return redirect()->route('artwork.index')->with('success', 'Artwork updated successfully.');
    }

    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->route('artwork.index')->with('success', 'Artwork deleted successfully.');
    }
}
