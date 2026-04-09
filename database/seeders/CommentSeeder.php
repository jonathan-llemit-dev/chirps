<?php

namespace Database\Seeders;

use App\Models\Chirp;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Seed sample comments for existing chirps.
     */
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            return;
        }

        Chirp::query()
            ->with('comments')
            ->get()
            ->each(function (Chirp $chirp) use ($users): void {
                if ($chirp->comments()->exists()) {
                    return;
                }

                $commentCount = fake()->numberBetween(1, 3);

                $users
                    ->shuffle()
                    ->take(min($commentCount, $users->count()))
                    ->each(function (User $user) use ($chirp): void {
                        Comment::query()->create([
                            'chirp_id' => $chirp->id,
                            'user_id' => $user->id,
                            'message' => fake()->sentence(
                                fake()->numberBetween(6, 12),
                            ),
                        ]);
                    });
            });
    }
}
