<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChirpRequest;
use App\Http\Requests\UpdateChirpRequest;
use App\Models\Chirp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class ChirpController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentUser = auth()->user();

        $chirps = Chirp::query()
            ->with([
                'user:id,name,email',
                'comments' => function ($query) use ($currentUser): void {
                    $query
                        ->with('user:id,name,email')
                        ->withCount('likes')
                        ->when(
                            $currentUser !== null,
                            fn ($commentQuery) => $commentQuery->withExists([
                                'likes as liked_by_current_user' => fn (
                                    Builder $likeQuery,
                                ) => $likeQuery->where(
                                    'user_id',
                                    $currentUser->id,
                                ),
                            ]),
                            fn ($commentQuery) => $commentQuery->selectRaw(
                                'false as liked_by_current_user',
                            ),
                        )
                        ->oldest();
                },
            ])
            ->withCount(['likes', 'comments'])
            ->when(
                $currentUser !== null,
                fn (Builder $query) => $query->withExists([
                    'likes as liked_by_current_user' => fn (
                        Builder $likeQuery,
                    ) => $likeQuery->where('user_id', $currentUser->id),
                ]),
                fn (Builder $query) => $query->selectRaw(
                    'false as liked_by_current_user',
                ),
            )
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

        return redirect()
            ->route('home')
            ->with('success', 'Your chirp has been posted!');
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
