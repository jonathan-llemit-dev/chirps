<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentLikeSeeder extends Seeder
{
    /**
     * Seed the application's database with sample likes for existing comments.
     */
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            return;
        }

        Comment::query()
            ->get()
            ->each(function (Comment $comment) use ($users): void {
                $availableUsers = $users->where('id', '!=', $comment->user_id);

                if ($availableUsers->isEmpty()) {
                    return;
                }

                $likerCount = min(
                    $availableUsers->count(),
                    fake()->numberBetween(0, 3),
                );

                if ($likerCount === 0) {
                    return;
                }

                $userIds = $availableUsers
                    ->shuffle()
                    ->take($likerCount)
                    ->pluck('id');

                foreach ($userIds as $userId) {
                    CommentLike::query()->firstOrCreate([
                        'comment_id' => $comment->id,
                        'user_id' => $userId,
                    ]);
                }
            });
    }
}
