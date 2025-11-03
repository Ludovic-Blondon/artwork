<?php

use App\Models\Artist;
use App\Models\User;

// ============================================
// AUTHENTICATION TESTS
// ============================================

describe('Authentication', function () {
    test('guests are redirected to login on index page', function () {
        $response = $this->get(route('artist.index'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on create page', function () {
        $response = $this->get(route('artist.create'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on edit page', function () {
        $artist = Artist::factory()->create();

        $response = $this->get(route('artist.edit', $artist));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on store', function () {
        $response = $this->post(route('artist.store'), [
            'name' => 'Test Artist',
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on update', function () {
        $artist = Artist::factory()->create();

        $response = $this->patch(route('artist.update', $artist), [
            'name' => 'Updated Name',
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on destroy', function () {
        $artist = Artist::factory()->create();

        $response = $this->delete(route('artist.destroy', $artist));
        $response->assertRedirect(route('login'));

        // Artist should still exist
        expect(Artist::find($artist->id))->not->toBeNull();
    });
});

// ============================================
// INDEX TESTS
// ============================================

describe('Index', function () {
    test('authenticated users can view artist index', function () {
        $user = User::factory()->create();
        Artist::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('artist.index'));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('Artist')
                ->has('paginatedArtists.data', 3)
            );
    });

    test('artist index shows pagination data', function () {
        $user = User::factory()->create();
        Artist::factory()->count(35)->create();

        $response = $this->actingAs($user)->get(route('artist.index'));

        $response->assertInertia(fn ($page) => $page
            ->component('Artist')
            ->has('paginatedArtists.data', 30) // 30 per page
            ->has('paginatedArtists.meta')
            ->where('paginatedArtists.meta.total', 35)
        );
    });
});

// ============================================
// CREATE TESTS
// ============================================

describe('Create', function () {
    test('authenticated users can view create form', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('artist.create'));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('ArtistForm')
                ->missing('artist')
            );
    });
});

// ============================================
// STORE TESTS
// ============================================

describe('Store', function () {
    test('can create artist with all fields', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Pablo Picasso',
            'bio' => 'Famous Spanish painter',
            'birth_date' => '1881-10-25',
            'death_date' => '1973-04-08',
        ]);

        $response->assertRedirect(route('artist.index'));
        $response->assertSessionHas('success', 'Artist created successfully.');

        expect(Artist::where('name', 'Pablo Picasso')->exists())->toBeTrue();

        $artist = Artist::where('name', 'Pablo Picasso')->first();
        expect($artist->bio)->toBe('Famous Spanish painter')
            ->and($artist->birth_date->format('Y-m-d'))->toBe('1881-10-25')
            ->and($artist->death_date->format('Y-m-d'))->toBe('1973-04-08');
    });

    test('can create artist with only name', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Minimal Artist',
        ]);

        $response->assertRedirect(route('artist.index'));

        expect(Artist::where('name', 'Minimal Artist')->exists())->toBeTrue();

        $artist = Artist::where('name', 'Minimal Artist')->first();
        expect($artist->bio)->toBeNull()
            ->and($artist->birth_date)->toBeNull()
            ->and($artist->death_date)->toBeNull();
    });

    test('name is required', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'bio' => 'Bio without name',
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    test('name cannot exceed 255 characters', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    test('birth_date must be a valid date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Test Artist',
            'birth_date' => 'not-a-date',
        ]);

        $response->assertSessionHasErrors(['birth_date']);
    });

    test('death_date must be a valid date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Test Artist',
            'death_date' => 'not-a-date',
        ]);

        $response->assertSessionHasErrors(['death_date']);
    });

    test('death_date must be after birth_date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Test Artist',
            'birth_date' => '2000-01-01',
            'death_date' => '1990-01-01',
        ]);

        $response->assertSessionHasErrors(['death_date']);
    });

    test('death_date can be same as birth_date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artist.store'), [
            'name' => 'Test Artist',
            'birth_date' => '2000-01-01',
            'death_date' => '2000-01-01',
        ]);

        $response->assertSessionHasErrors(['death_date']);
    });
});

// ============================================
// EDIT TESTS
// ============================================

describe('Edit', function () {
    test('authenticated users can view edit form', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create([
            'name' => 'Test Artist',
            'bio' => 'Test bio',
        ]);

        $response = $this->actingAs($user)->get(route('artist.edit', $artist));

        $response->assertStatus(200)
            ->assertInertia(fn ($page) => $page
                ->component('ArtistForm')
                ->has('artist')
                ->where('artist.id', $artist->id)
                ->where('artist.name', 'Test Artist')
                ->where('artist.bio', 'Test bio')
            );
    });

    test('returns 404 for non-existent artist', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('artist.edit', 99999));

        $response->assertNotFound();
    });
});

// ============================================
// UPDATE TESTS
// ============================================

describe('Update', function () {
    test('can update artist with all fields', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create([
            'name' => 'Old Name',
            'bio' => 'Old bio',
        ]);

        $response = $this->actingAs($user)->patch(route('artist.update', $artist), [
            'name' => 'New Name',
            'bio' => 'New bio',
            'birth_date' => '1900-01-01',
            'death_date' => '2000-01-01',
        ]);

        $response->assertRedirect(route('artist.index'));
        $response->assertSessionHas('success', 'Artist updated successfully.');

        $artist->refresh();
        expect($artist->name)->toBe('New Name')
            ->and($artist->bio)->toBe('New bio')
            ->and($artist->birth_date->format('Y-m-d'))->toBe('1900-01-01')
            ->and($artist->death_date->format('Y-m-d'))->toBe('2000-01-01');
    });

    test('can update only name', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create([
            'name' => 'Old Name',
            'bio' => 'Original bio',
        ]);

        $response = $this->actingAs($user)->patch(route('artist.update', $artist), [
            'name' => 'Updated Name',
            'bio' => 'Original bio',
        ]);

        $response->assertRedirect(route('artist.index'));

        $artist->refresh();
        expect($artist->name)->toBe('Updated Name')
            ->and($artist->bio)->toBe('Original bio');
    });

    test('name is required on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->patch(route('artist.update', $artist), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    });

    test('death_date must be after birth_date on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->patch(route('artist.update', $artist), [
            'name' => 'Test',
            'birth_date' => '2000-01-01',
            'death_date' => '1990-01-01',
        ]);

        $response->assertSessionHasErrors(['death_date']);
    });

    test('returns 404 for non-existent artist', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('artist.update', 99999), [
            'name' => 'Test',
        ]);

        $response->assertNotFound();
    });
});

// ============================================
// DESTROY TESTS
// ============================================

describe('Destroy', function () {
    test('authenticated users can delete an artist', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->delete(route('artist.destroy', $artist));

        $response->assertRedirect(route('artist.index'));
        $response->assertSessionHas('success', 'Artist deleted successfully.');

        expect(Artist::find($artist->id))->toBeNull();
    });

    test('returns 404 when deleting non-existent artist', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('artist.destroy', 99999));

        $response->assertNotFound();
    });

    test('artist count decreases after deletion', function () {
        $user = User::factory()->create();
        Artist::factory()->count(5)->create();
        $artist = Artist::first();

        expect(Artist::count())->toBe(5);

        $this->actingAs($user)->delete(route('artist.destroy', $artist));

        expect(Artist::count())->toBe(4);
    });
});
