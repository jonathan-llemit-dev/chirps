<?php

namespace Database\Factories;

use App\Models\Chirp;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Like>
 */
class LikeFactory extends Factory
{
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chirp_id' => Chirp::factory(),
            'user_id' => User::factory(),
        ];
    }
}
