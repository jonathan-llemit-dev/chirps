<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a newly created comment for the given chirp.
     */
    public function store(
        StoreCommentRequest $request,
        Chirp $chirp,
    ): RedirectResponse {
        $chirp->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return redirect()->route('home')->with('success', 'Comment posted!');
    }
}
