<?php

use App\Models\Artist;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('artist.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the artist page', function () {
    $this->seed();

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('artist.index'));
    $response->assertStatus(200)
        ->assertInertia(
            fn ($page) => $page
                ->component('Artist')
                ->has('paginatedArtists.data', 2)
        );
});

test('guests cannot delete artists', function () {
    $this->seed();
    $artist = Artist::first();
    $response = $this->delete(route('artist.destroy', $artist));

    $response->assertRedirect(route('login'));
    $this->assertModelExists($artist);
});

test('authenticated users can delete an artist', function () {
    $this->seed();

    $user = User::factory()->create();
    $artist = Artist::first();

    $this->actingAs($user);

    $response = $this->delete(route('artist.destroy', $artist));

    $response->assertRedirect(route('artist.index'));
    $response->assertSessionHas('success', 'Artist deleted successfully.');
    $this->assertModelMissing($artist);
});

test('deleting a non-existent artist returns 404', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->delete(route('artist.destroy', 99999));

    $response->assertNotFound();
});
