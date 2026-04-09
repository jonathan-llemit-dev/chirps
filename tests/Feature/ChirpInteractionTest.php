<?php

use App\Models\Chirp;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;

it('allows an authenticated user to comment on a chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs($user)
        ->post(route('chirps.comments.store', $chirp), [
            'message' => 'Nice chirp!',
        ])
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('comments', [
        'chirp_id' => $chirp->id,
        'user_id' => $user->id,
        'message' => 'Nice chirp!',
    ]);
});

it('validates comment message when creating a comment', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs($user)
        ->post(route('chirps.comments.store', $chirp), [
            'message' => '',
        ])
        ->assertSessionHasErrors(['message']);

    expect(Comment::count())->toBe(0);
});

it('redirects guests when attempting to comment on a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->post(route('chirps.comments.store', $chirp), [
        'message' => 'Guest comment',
    ])->assertRedirect('/login');

    expect(Comment::count())->toBe(0);
});

it('allows an authenticated user to like a chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs($user)
        ->post(route('chirps.likes.store', $chirp))
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('likes', [
        'chirp_id' => $chirp->id,
        'user_id' => $user->id,
    ]);
});

it('does not create duplicate likes for the same user and chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs($user)
        ->post(route('chirps.likes.store', $chirp))
        ->assertRedirect(route('home'));

    $this->actingAs($user)
        ->post(route('chirps.likes.store', $chirp))
        ->assertRedirect(route('home'));

    expect(
        Like::query()
            ->where('chirp_id', $chirp->id)
            ->where('user_id', $user->id)
            ->count(),
    )->toBe(1);
});

it('allows an authenticated user to unlike a chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    Like::factory()->create([
        'chirp_id' => $chirp->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('chirps.likes.destroy', $chirp))
        ->assertRedirect(route('home'));

    $this->assertDatabaseMissing('likes', [
        'chirp_id' => $chirp->id,
        'user_id' => $user->id,
    ]);
});

it('redirects guests when attempting to like or unlike a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->post(route('chirps.likes.store', $chirp))->assertRedirect('/login');

    $this->delete(route('chirps.likes.destroy', $chirp))->assertRedirect(
        '/login',
    );
});

it('shows comment and like counts on the home page', function () {
    $chirp = Chirp::factory()
        ->for(
            User::factory()->create([
                'name' => 'Chirp Owner',
                'email' => 'owner@example.com',
            ]),
        )
        ->create([
            'message' => 'Countable chirp',
        ]);

    Like::factory()
        ->count(2)
        ->create([
            'chirp_id' => $chirp->id,
        ]);

    Comment::factory()
        ->count(3)
        ->create([
            'chirp_id' => $chirp->id,
        ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Countable chirp')
        ->assertSee('2 likes')
        ->assertSee('3 comments');
});

it('shows comments inside a collapsed toggle on the home page', function () {
    $chirp = Chirp::factory()
        ->for(User::factory()->create())
        ->create([
            'message' => 'Discuss this chirp',
        ]);

    $commenter = User::factory()->create([
        'name' => 'Commenter Name',
        'email' => 'commenter@example.com',
    ]);

    Comment::factory()->create([
        'chirp_id' => $chirp->id,
        'user_id' => $commenter->id,
        'message' => 'This is a visible comment.',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Discuss this chirp')
        ->assertSee('Commenter Name')
        ->assertSee('This is a visible comment.')
        ->assertSee('1 comment')
        ->assertSee('<details', false)
        ->assertSee('<summary', false)
        ->assertDontSee('<details open', false);
});

it(
    'does not show guest chirp interaction prompts on the home page',
    function () {
        $chirp = Chirp::factory()
            ->for(User::factory()->create())
            ->create([
                'message' => 'Readable guest chirp',
            ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Readable guest chirp')
            ->assertDontSee('Sign in to like')
            ->assertDontSee('to comment on this chirp.');
    },
);

it('renders chirp content without a leading blank line', function () {
    $chirp = Chirp::factory()
        ->for(User::factory()->create())
        ->create([
            'message' => 'Flush chirp content',
        ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('>Flush chirp content<', false);
});
