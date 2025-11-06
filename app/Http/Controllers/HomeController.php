<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtworkResource;
use App\Models\Artwork;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'paginatedArtworks' => ArtworkResource::collection(
                Artwork::with('artist')->paginate(30)
            ),
        ]);
    }
}
