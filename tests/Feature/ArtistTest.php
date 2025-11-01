<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('artist'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the artist page', function () {
    $this->seed();

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('artist'));
    $response->assertStatus(200)
        ->assertInertia(
            fn ($page) => $page
                ->component('Artist')
                ->has('paginatedArtists.data', 2)
        );
});
