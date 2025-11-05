<?php

use App\Models\Artist;
use App\Models\User;
use App\Models\Work;

// ============================================
// AUTHENTICATION TESTS
// ============================================

describe('Authentication', function () {
    test('guests are redirected to login on index page', function () {
        $response = $this->get(route('work.index'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on create page', function () {
        $response = $this->get(route('work.create'));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on edit page', function () {
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->get(route('work.edit', $work));
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on store', function () {
        $artist = Artist::factory()->create();

        $response = $this->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on update', function () {
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->patch(route('work.update', $work), [
            'title' => 'Updated Title',
            'artist_id' => $artist->id,
        ]);
        $response->assertRedirect(route('login'));
    });

    test('guests are redirected to login on destroy', function () {
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->delete(route('work.destroy', $work));
        $response->assertRedirect(route('login'));

        // Work should still exist
        expect(Work::find($work->id))->not->toBeNull();
    });
});

// ============================================
// INDEX TESTS
// ============================================

describe('Index', function () {
    test('authenticated users can view work index', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Work::factory()->count(3)->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('work.index'));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('work/Index')
                    ->has('paginatedWorks.data', 3)
            );
    });

    test('work index shows pagination data', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Work::factory()->count(35)->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('work.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('work/Index')
                ->has('paginatedWorks.data', 30) // 30 per page
                ->has('paginatedWorks.meta')
                ->where('paginatedWorks.meta.total', 35)
        );
    });

    test('work index includes artist relationship', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Pablo Picasso']);
        Work::factory()->create([
            'title' => 'Guernica',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->get(route('work.index'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('work/Index')
                ->has('paginatedWorks.data.0.artist')
                ->where('paginatedWorks.data.0.artist.name', 'Pablo Picasso')
        );
    });
});

// ============================================
// CREATE TESTS
// ============================================

describe('Create', function () {
    test('authenticated users can view create form', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('work.create'));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('work/Form')
                    ->has('artists')
                    ->missing('work')
            );
    });

    test('create form includes all artists', function () {
        $user = User::factory()->create();
        Artist::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('work.create'));

        $response->assertInertia(
            fn ($page) => $page
                ->component('work/Form')
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

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Guernica',
            'description' => 'Famous anti-war painting',
            'year_created' => 1937,
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('work.index'));
        $response->assertSessionHas('success', 'Work created successfully.');

        expect(Work::where('title', 'Guernica')->exists())->toBeTrue();

        $work = Work::where('title', 'Guernica')->first();
        expect($work->description)->toBe('Famous anti-war painting')
            ->and($work->year_created)->toBe(1937)
            ->and($work->artist_id)->toBe($artist->id);
    });

    test('can create work with only required fields', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Minimal Work',
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('work.index'));

        expect(Work::where('title', 'Minimal Work')->exists())->toBeTrue();

        $work = Work::where('title', 'Minimal Work')->first();
        expect($work->description)->toBeNull()
            ->and($work->year_created)->toBeNull()
            ->and($work->artist_id)->toBe($artist->id);
    });

    test('title is required', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'description' => 'Description without title',
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('title cannot exceed 255 characters', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => str_repeat('a', 256),
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('artist_id is required', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('artist_id must exist in artists table', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('year_created must be an integer', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
            'year_created' => 'not-a-year',
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created must be at least 1', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
            'year_created' => 0,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created cannot exceed 9999', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
            'year_created' => 10000,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('year_created accepts valid years', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->post(route('work.store'), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
            'year_created' => 2024,
        ]);

        $response->assertRedirect(route('work.index'));
        expect(Work::where('year_created', 2024)->exists())->toBeTrue();
    });
});

// ============================================
// EDIT TESTS
// ============================================

describe('Edit', function () {
    test('authenticated users can view edit form', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Pablo Picasso']);
        $work = Work::factory()->create([
            'title' => 'Guernica',
            'description' => 'Test description',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->get(route('work.edit', $work));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('work/Form')
                    ->has('work')
                    ->has('artists')
                    ->where('work.id', $work->id)
                    ->where('work.title', 'Guernica')
                    ->where('work.description', 'Test description')
            );
    });

    test('edit form includes all artists', function () {
        $user = User::factory()->create();
        Artist::factory()->count(5)->create();
        $artist = Artist::first();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('work.edit', $work));

        $response->assertInertia(
            fn ($page) => $page
                ->component('work/Form')
                ->has('artists', 5)
        );
    });

    test('edit form includes work artist relationship', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create(['name' => 'Vincent van Gogh']);
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->get(route('work.edit', $work));

        $response->assertInertia(
            fn ($page) => $page
                ->component('work/Form')
                ->has('work.artist')
                ->where('work.artist.name', 'Vincent van Gogh')
        );
    });

    test('returns 404 for non-existent work', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('work.edit', 99999));

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
        $work = Work::factory()->create([
            'title' => 'Old Title',
            'description' => 'Old description',
            'year_created' => 2000,
            'artist_id' => $oldArtist->id,
        ]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'New Title',
            'description' => 'New description',
            'year_created' => 2024,
            'artist_id' => $newArtist->id,
        ]);

        $response->assertRedirect(route('work.index'));
        $response->assertSessionHas('success', 'Work updated successfully.');

        $work->refresh();
        expect($work->title)->toBe('New Title')
            ->and($work->description)->toBe('New description')
            ->and($work->year_created)->toBe(2024)
            ->and($work->artist_id)->toBe($newArtist->id);
    });

    test('can update only title', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create([
            'title' => 'Old Title',
            'description' => 'Original description',
            'artist_id' => $artist->id,
        ]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'Updated Title',
            'description' => 'Original description',
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('work.index'));

        $work->refresh();
        expect($work->title)->toBe('Updated Title')
            ->and($work->description)->toBe('Original description');
    });

    test('can change artist', function () {
        $user = User::factory()->create();
        $artist1 = Artist::factory()->create(['name' => 'Artist 1']);
        $artist2 = Artist::factory()->create(['name' => 'Artist 2']);
        $work = Work::factory()->create([
            'title' => 'Test Work',
            'artist_id' => $artist1->id,
        ]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'Test Work',
            'artist_id' => $artist2->id,
        ]);

        $response->assertRedirect(route('work.index'));

        $work->refresh();
        expect($work->artist_id)->toBe($artist2->id);
    });

    test('title is required on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => '',
            'artist_id' => $artist->id,
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('artist_id is required on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'Test Work',
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('artist_id must exist on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'Test Work',
            'artist_id' => 99999,
        ]);

        $response->assertSessionHasErrors(['artist_id']);
    });

    test('year_created validation applies on update', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->patch(route('work.update', $work), [
            'title' => 'Test Work',
            'artist_id' => $artist->id,
            'year_created' => 10000,
        ]);

        $response->assertSessionHasErrors(['year_created']);
    });

    test('returns 404 for non-existent work', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->actingAs($user)->patch(route('work.update', 99999), [
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
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $response = $this->actingAs($user)->delete(route('work.destroy', $work));

        $response->assertRedirect(route('work.index'));
        $response->assertSessionHas('success', 'Work deleted successfully.');

        expect(Work::find($work->id))->toBeNull();
    });

    test('returns 404 when deleting non-existent work', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('work.destroy', 99999));

        $response->assertNotFound();
    });

    test('work count decreases after deletion', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        Work::factory()->count(5)->create(['artist_id' => $artist->id]);

        expect(Work::count())->toBe(5);

        $work = Work::first();
        $this->actingAs($user)->delete(route('work.destroy', $work));

        expect(Work::count())->toBe(4);
    });

    test('deleting work does not delete associated artist', function () {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $work = Work::factory()->create(['artist_id' => $artist->id]);

        $this->actingAs($user)->delete(route('work.destroy', $work));

        expect(Artist::find($artist->id))->not->toBeNull();
    });
});
