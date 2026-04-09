<?php

namespace Database\Seeders;

use App\Models\Chirp;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Seed the application's database with sample likes for existing chirps.
     */
    public function run(): void
    {
        $users = User::query()->get();

        if ($users->isEmpty()) {
            return;
        }

        Chirp::query()
            ->with('likes')
            ->get()
            ->each(function (Chirp $chirp) use ($users): void {
                $likerCount = min($users->count(), fake()->numberBetween(0, 3));

                if ($likerCount === 0) {
                    return;
                }

                $userIds = $users
                    ->where('id', '!=', $chirp->user_id)
                    ->shuffle()
                    ->take($likerCount)
                    ->pluck('id');

                foreach ($userIds as $userId) {
                    Like::query()->firstOrCreate([
                        'chirp_id' => $chirp->id,
                        'user_id' => $userId,
                    ]);
                }
            });
    }
}
