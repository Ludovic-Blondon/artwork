<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('work.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the work page', function () {
    $this->seed();

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('work.index'));
    $response->assertStatus(200)
        ->assertInertia(
            fn ($page) => $page
                ->component('work/Index')
                ->has('paginatedWorks.data', 3)
        );
});
