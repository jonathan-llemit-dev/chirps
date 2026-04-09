<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;

class LikeController extends Controller
{
    /**
     * Store a new like for the given chirp.
     */
    public function store(Chirp $chirp): RedirectResponse
    {
        $chirp->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('home')->with('success', 'Chirp liked!');
    }

    /**
     * Remove the authenticated user's like from the given chirp.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        $chirp->likes()->where('user_id', auth()->id())->delete();

        return redirect()->route('home')->with('success', 'Like removed!');
    }
}
