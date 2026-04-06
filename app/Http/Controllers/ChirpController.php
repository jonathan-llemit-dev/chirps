<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChirpRequest;
use App\Http\Requests\UpdateChirpRequest;
use App\Models\Chirp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChirpController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chirps = Chirp::with('user:id,name')
            ->latest()
            ->limit(50)
            ->get();

        return view('home', compact('chirps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChirpRequest $request)
    {
        auth()->user()->chirps()->create($request->validated());

        return redirect()->route('home')->with('success', 'Your chirp has been posted!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', compact('chirp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChirpRequest $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        $chirp->update($request->validated());

        return redirect()->route('home')->with('success', 'Chirp updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return redirect()->route('home')->with('success', 'Chirp deleted!');
    }
}
