<?php

test('returns a successful response', function () {
    $this->seed();

    $response = $this->get(route('home'));

    $response->assertStatus(200)
        ->assertInertia(
            fn ($page) => $page
                ->component('Welcome')
                ->has('paginatedArtworks.data', 3)
                ->has('canRegister')
        );
});
