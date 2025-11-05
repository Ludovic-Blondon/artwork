<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\WorkResource;
use App\Models\Artist;
use App\Models\Work;
use Inertia\Inertia;

class WorkController extends Controller
{
    public function index()
    {
        return Inertia::render('work/Index', [
            'paginatedWorks' => WorkResource::collection(
                Work::with('artist')->paginate(30)
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('work/Form', [
            'artists' => ArtistResource::collection(Artist::all())->resolve(),
        ]);
    }

    public function store(StoreWorkRequest $request)
    {
        Work::create($request->validated());

        return redirect()->route('work.index')->with('success', 'Work created successfully.');
    }

    public function edit(Work $work)
    {
        return Inertia::render('work/Form', [
            'work' => WorkResource::make($work->load('artist'))->resolve(),
            'artists' => ArtistResource::collection(Artist::all())->resolve(),
        ]);
    }

    public function update(UpdateWorkRequest $request, Work $work)
    {
        $work->update($request->validated());

        return redirect()->route('work.index')->with('success', 'Work updated successfully.');
    }

    public function destroy(Work $work)
    {
        $work->delete();

        return redirect()->route('work.index')->with('success', 'Work deleted successfully.');
    }
}
