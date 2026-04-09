<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;

class CommentLikeController extends Controller
{
    /**
     * Store a new like for the given comment.
     */
    public function store(Comment $comment): RedirectResponse
    {
        $comment->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('home')->with('success', 'Comment liked!');
    }

    /**
     * Remove the authenticated user's like from the given comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->likes()->where('user_id', auth()->id())->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Comment like removed!');
    }
}
