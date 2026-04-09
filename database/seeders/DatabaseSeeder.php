<?php

namespace Database\Seeders;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'moderator']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ],
        );
        $admin->assignRole('admin');

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ],
        );
        $user->assignRole('user');

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('password'),
            ],
        );
        $moderator->assignRole('moderator');

        if ($admin->chirps()->count() === 0) {
            Chirp::factory()->for($admin)->count(3)->create();
        }

        if ($user->chirps()->count() === 0) {
            Chirp::factory()->for($user)->count(3)->create();
        }

        if ($moderator->chirps()->count() === 0) {
            Chirp::factory()->for($moderator)->count(2)->create();
        }

        $this->call([CommentSeeder::class, LikeSeeder::class]);
    }
}
