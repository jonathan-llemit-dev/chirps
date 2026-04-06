<?php

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('allows a registered user to login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('allows an admin to update any chirp', function () {
    Role::create(['name' => 'admin']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $owner = User::factory()->create();
    $chirp = Chirp::factory()->for($owner)->create();

    $this->actingAs($admin)
        ->put("/chirps/{$chirp->id}", [
            'message' => 'Updated by admin',
        ])
        ->assertRedirect('/');
});

it('redirects guests from protected chirp actions', function () {
    $owner = User::factory()->create();
    $chirp = Chirp::factory()->for($owner)->create();

    $this->get("/chirps/{$chirp->id}/edit")
        ->assertRedirect('/login');

    $this->delete("/chirps/{$chirp->id}")
        ->assertRedirect('/login');
});

it('shows edit and delete buttons to admin for any chirp', function () {
    Role::create(['name' => 'admin']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $owner = User::factory()->create();
    $chirp = Chirp::factory()->for($owner)->create(['message' => 'Admin can edit']);

    $this->actingAs($admin)
        ->get('/')
        ->assertSee("/chirps/{$chirp->id}/edit")
        ->assertSee('Edit')
        ->assertSee('Delete');
});

it('allows a guest to register and logout successfully', function () {
    $this->post('/register', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'qweqweQ1!',
        'password_confirmation' => 'qweqweQ1!',
    ])->assertRedirect('/');

    $user = User::where('email', 'new@example.com')->first();
    $this->assertAuthenticatedAs($user);

    $this->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

it('redirects guests to login when attempting to post a chirp', function () {
    $this->post('/chirps', ['message' => 'Hello from guest'])
        ->assertRedirect('/login');
});
