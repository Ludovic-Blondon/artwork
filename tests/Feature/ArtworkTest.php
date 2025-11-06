<?php

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;

// ============================================
// AUTHENTICATION TESTS
// ============================================

describe('Authentication', function () {
    test('guests are redirected to login on index page', function () {
        $response = $this->get(route('artwork.index'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on create page', function () {
        $response = $this->get(route('artwork.create'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on edit page', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->get(route('artwork.edit', $artwork));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on store', function () {
        $artist = Artist::factory()->create();

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on update', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->patch(route('artwork.update', $artwork), [
            'title' => 'Updated Title',
            'artist_id' => $artist->id,
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on destroy', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->delete(route('artwork.destroy', $artwork));
        $response->assertRedirect(route('login'));

        // Work should still exist
        expect(Artwork::find($artwork->id))->not->toBeNull();
    });
});

// ============================================
// INDEX TESTS
// ============================================

describe('Index', function () {
    test('authenticated users can view work index', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Artwork::factory()->count(3)->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('artwork.index'));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('artwork/Index')
                    ->has('paginatedArtworks.data', 3)
            );
    });

    test('work index shows pagination data', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Artwork::factory()->count(35)->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('artwork.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('artwork/Index')
                ->has('paginatedArtworks.data', 30) // 30 per page
                ->has('paginatedArtworks.meta')
                ->where('paginatedArtworks.meta.total', 35)
        );
    });

    test('work index includes artist relationship', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Pablo Picasso']);
        Artwork::factory()->create([
            'title' => 'Guernica',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->get(route('artwork.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('artwork/Index')
                ->has('paginatedArtworks.data.0.artist')
                ->where('paginatedArtworks.data.0.artist.name', 'Pablo Picasso')
        );
    });
});

// ============================================
// CREATE TESTS
// ============================================

describe('Create', function () {
    test('authenticated users can view create form', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('artwork.create'));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('artwork/Form')
                    ->has('artists')
                    ->missing('artwork')
            );
    });

    test('create form includes all artists', function () {
        $user = User::factory()->create();
        Artist::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('artwork.create'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('artwork/Form')
                ->has('artists', 3)
        );
    });
});

// ============================================
// STORE TESTS
// ============================================

describe('Store', function () {
    test('can create work with all fields', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Pablo Picasso']);

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Guernica',
            'description' => 'Famous anti-war painting',
            'year_created' => 1937,
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('artwork.index'));
        $response->assertSessionHas('success', 'Artwork created successfully.');

        expect(Artwork::where('title', 'Guernica')->exists())->toBeTrue();

        $artwork = Artwork::where('title', 'Guernica')->first();
        expect($artwork->description)->toBe('Famous anti-war painting')
            ->and($artwork->year_created)->toBe(1937)
            ->and($artwork->artist_id)->toBe($artist->id);
    });

    test('can create work with only required fields', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Minimal Artwork',
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('artwork.index'));

        expect(Artwork::where('title', 'Minimal Artwork')->exists())->toBeTrue();

        $artwork = Artwork::where('title', 'Minimal Artwork')->first();
        expect($artwork->description)->toBeNull()
            ->and($artwork->year_created)->toBeNull()
            ->and($artwork->artist_id)->toBe($artist->id);
    });

    test('title is required', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'description' => 'Description without title',
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('title cannot exceed 255 characters', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => str_repeat('a', 256),
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('artist_id is required', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('artist_id must exist in artists table', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('year_created must be an integer', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'year_created' => 'not-a-year',
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created must be at least 1', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'year_created' => 0,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created cannot exceed 9999', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'year_created' => 10000,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created accepts valid years', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'year_created' => 2024,
        ]);

        $response->assertRedirect(route('artwork.index'));
        expect(Artwork::where('year_created', 2024)->exists())->toBeTrue();
    });
});

// ============================================
// EDIT TESTS
// ============================================

describe('Edit', function () {
    test('authenticated users can view edit form', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Pablo Picasso']);
        $artwork = Artwork::factory()->create([
            'title' => 'Guernica',
            'description' => 'Test description',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->get(route('artwork.edit', $artwork));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('artwork/Form')
                    ->has('artwork')
                    ->has('artists')
                    ->where('artwork.id', $artwork->id)
                    ->where('artwork.title', 'Guernica')
                    ->where('artwork.description', 'Test description')
            );
    });

    test('edit form includes all artists', function () {
        $user = User::factory()->create();
        Artist::factory()->count(5)->create();
        $artist = Artist::first();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('artwork.edit', $artwork));

        $response->assertInertia(
            fn ($page) => $page
                ->component('artwork/Form')
                ->has('artists', 5)
        );
    });

    test('edit form includes work artist relationship', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Vincent van Gogh']);
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('artwork.edit', $artwork));

        $response->assertInertia(
            fn ($page) => $page
                ->component('artwork/Form')
                ->has('artwork.artist')
                ->where('artwork.artist.name', 'Vincent van Gogh')
        );
    });

    test('returns 404 for non-existent work', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('artwork.edit', 99999));

        $response->assertNotFound();
    });
});

// ============================================
// UPDATE TESTS
// ============================================

describe('Update', function () {
    test('can update work with all fields', function () {
        $user = User::factory()->create();
        $oldArtist = Artist::factory()->create(['name' => 'Old Artist']);
        $newArtist = Artist::factory()->create(['name' => 'New Artist']);
        $artwork = Artwork::factory()->create([
            'title' => 'Old Title',
            'description' => 'Old description',
            'year_created' => 2000,
            'artist_id' => $oldArtist->id,
        ]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'New Title',
            'description' => 'New description',
            'year_created' => 2024,
            'artist_id' => $newArtist->id,
        ]);

        $response->assertRedirect(route('artwork.index'));
        $response->assertSessionHas('success', 'Artwork updated successfully.');

        $artwork->refresh();
        expect($artwork->title)->toBe('New Title')
            ->and($artwork->description)->toBe('New description')
            ->and($artwork->year_created)->toBe(2024)
            ->and($artwork->artist_id)->toBe($newArtist->id);
    });

    test('can update only title', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'title' => 'Old Title',
            'description' => 'Original description',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'Updated Title',
            'description' => 'Original description',
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('artwork.index'));

        $artwork->refresh();
        expect($artwork->title)->toBe('Updated Title')
            ->and($artwork->description)->toBe('Original description');
    });

    test('can change artist', function () {
        $user = User::factory()->create();
        $artist1 = Artist::factory()->create(['name' => 'Artist 1']);
        $artist2 = Artist::factory()->create(['name' => 'Artist 2']);
        $artwork = Artwork::factory()->create([
            'title' => 'Test Artwork',
            'artist_id' => $artist1->id,
        ]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'Test Artwork',
            'artist_id' => $artist2->id,
        ]);

        $response->assertRedirect(route('artwork.index'));

        $artwork->refresh();
        expect($artwork->artist_id)->toBe($artist2->id);
    });

    test('title is required on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => '',
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('artist_id is required on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'Test Artwork',
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('artist_id must exist on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'Test Artwork',
            'artist_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('year_created validation applies on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('artwork.update', $artwork), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'year_created' => 10000,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('returns 404 for non-existent work', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->patch(route('artwork.update', 99999), [
            'title' => 'Test',
            'artist_id' => $artist->id,
        ]);

        $response->assertNotFound();
    });
});

// ============================================
// DESTROY TESTS
// ============================================

describe('Destroy', function () {
    test('authenticated users can delete a work', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->delete(route('artwork.destroy', $artwork));

        $response->assertRedirect(route('artwork.index'));
        $response->assertSessionHas('success', 'Artwork deleted successfully.');

        expect(Artwork::find($artwork->id))->toBeNull();
    });

    test('returns 404 when deleting non-existent work', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('artwork.destroy', 99999));

        $response->assertNotFound();
    });

    test('work count decreases after deletion', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Artwork::factory()->count(5)->create(['artist_id' => $artist->id]);

        expect(Artwork::count())->toBe(5);

        $artwork = Artwork::first();
        $this->actingAs($user)->delete(route('artwork.destroy', $artwork));

        expect(Artwork::count())->toBe(4);
    });

    test('deleting work does not delete associated artist', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create(['artist_id' => $artist->id]);

        $this->actingAs($user)->delete(route('artwork.destroy', $artwork));

        expect(Artist::find($artist->id))->not->toBeNull();
    });
});
