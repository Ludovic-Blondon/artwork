<?php

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ============================================
// AUTHENTICATION TESTS
// ============================================

describe('Authentication', function () {
    beforeEach(function () {
        Storage::fake('public');
    });

    test('guests cannot upload images when creating artwork', function () {
        $artist = Artist::factory()->create();
        $image = UploadedFile::fake()->image('artwork.jpg');

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'images' => [$image],
        ]);

        $response->assertRedirect(route('login'));
        expect(Artwork::count())->toBe(0);
    });

    test('guests cannot add images to existing artwork', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image = UploadedFile::fake()->image('new.jpg');

        $response = $this->patch(route('artwork.update', $artwork), [
            'title' => $artwork->title,
            'artist_id' => $artwork->artist_id,
            'images' => [$image],
        ]);

        $response->assertRedirect(route('login'));
        expect($artwork->fresh()->getMedia('images'))->toHaveCount(0);
    });

    test('guests cannot delete images from artwork', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image = UploadedFile::fake()->image('image.jpg');
        $media = $artwork->addMedia($image)->toMediaCollection('images');

        $response = $this->patch(route('artwork.update', $artwork), [
            'title' => $artwork->title,
            'artist_id' => $artwork->artist_id,
            'deleted_media_ids' => [$media->id],
        ]);

        $response->assertRedirect(route('login'));
        expect($artwork->fresh()->getMedia('images'))->toHaveCount(1);
    });

    test('guests cannot delete artwork with media', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image = UploadedFile::fake()->image('image.jpg');
        $artwork->addMedia($image)->toMediaCollection('images');

        $response = $this->delete(route('artwork.destroy', $artwork));

        $response->assertRedirect(route('login'));
        expect(Artwork::find($artwork->id))->not->toBeNull();
        expect($artwork->fresh()->getMedia('images'))->toHaveCount(1);
    });
});

// ============================================
// UPLOAD TESTS
// ============================================

describe('Upload', function () {
    beforeEach(function () {
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    test('can upload images when creating artwork', function () {
        $artist = Artist::factory()->create();
        $image1 = UploadedFile::fake()->image('artwork1.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('artwork2.jpg', 800, 600);

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'description' => 'Test Description',
            'year_created' => 2024,
            'artist_id' => $artist->id,
            'images' => [$image1, $image2],
        ]);

        $response->assertRedirect(route('artwork.index'));
        $response->assertSessionHas('success');

        $artwork = Artwork::where('title', 'Test Artwork')->first();
        expect($artwork)->not->toBeNull();
        expect($artwork->getMedia('images'))->toHaveCount(2);
    });

    test('can create artwork without images', function () {
        $artist = Artist::factory()->create();

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork No Images',
            'description' => 'Description',
            'year_created' => 2024,
            'artist_id' => $artist->id,
        ]);

        $response->assertRedirect(route('artwork.index'));

        $artwork = Artwork::where('title', 'Test Artwork No Images')->first();
        expect($artwork)->not->toBeNull();
        expect($artwork->getMedia('images'))->toHaveCount(0);
    });

    test('can add images to existing artwork', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $existingImage = UploadedFile::fake()->image('existing.jpg');
        $artwork->addMedia($existingImage)->toMediaCollection('images');

        $newImage1 = UploadedFile::fake()->image('new1.jpg');
        $newImage2 = UploadedFile::fake()->image('new2.jpg');

        $response = $this->patch(route('artwork.update', $artwork), [
            'title' => $artwork->title,
            'description' => $artwork->description,
            'year_created' => $artwork->year_created,
            'artist_id' => $artwork->artist_id,
            'images' => [$newImage1, $newImage2],
        ]);

        $response->assertRedirect(route('artwork.index'));

        $artwork->refresh();
        expect($artwork->getMedia('images'))->toHaveCount(3);
    });
});

// ============================================
// DELETE TESTS
// ============================================

describe('Delete', function () {
    beforeEach(function () {
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    test('can delete images from artwork', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image1 = UploadedFile::fake()->image('image1.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');
        $media1 = $artwork->addMedia($image1)->toMediaCollection('images');
        $media2 = $artwork->addMedia($image2)->toMediaCollection('images');

        expect($artwork->getMedia('images'))->toHaveCount(2);

        $response = $this->patch(route('artwork.update', $artwork), [
            'title' => $artwork->title,
            'description' => $artwork->description,
            'year_created' => $artwork->year_created,
            'artist_id' => $artwork->artist_id,
            'deleted_media_ids' => [$media1->id],
        ]);

        $response->assertRedirect(route('artwork.index'));

        $artwork->refresh();
        expect($artwork->getMedia('images'))->toHaveCount(1);
        expect($artwork->getMedia('images')->first()->id)->toBe($media2->id);
    });

    test('deleting artwork also deletes its media', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image1 = UploadedFile::fake()->image('image1.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');
        $artwork->addMedia($image1)->toMediaCollection('images');
        $artwork->addMedia($image2)->toMediaCollection('images');

        $mediaIds = $artwork->getMedia('images')->pluck('id')->toArray();

        expect($artwork->getMedia('images'))->toHaveCount(2);

        $response = $this->delete(route('artwork.destroy', $artwork));

        $response->assertRedirect(route('artwork.index'));

        expect(Artwork::find($artwork->id))->toBeNull();

        // Verify media records are deleted
        foreach ($mediaIds as $mediaId) {
            expect(\Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId))->toBeNull();
        }
    });
});

// ============================================
// VALIDATION TESTS
// ============================================

describe('Validation', function () {
    beforeEach(function () {
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    test('validates image file types', function () {
        $artist = Artist::factory()->create();
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'images' => [$invalidFile],
        ]);

        $response->assertSessionHasErrors('images.0');
    });

    test('validates image file size', function () {
        $artist = Artist::factory()->create();
        // Create a file larger than 5MB (5120 KB)
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(6000);

        $response = $this->post(route('artwork.store'), [
            'title' => 'Test Artwork',
            'artist_id' => $artist->id,
            'images' => [$largeFile],
        ]);

        $response->assertSessionHasErrors('images.0');
    });

    test('accepts valid image formats', function () {
        $artist = Artist::factory()->create();

        $formats = ['jpeg', 'jpg', 'png', 'webp'];

        foreach ($formats as $format) {
            $image = UploadedFile::fake()->image("test.{$format}");

            $response = $this->post(route('artwork.store'), [
                'title' => "Test Artwork {$format}",
                'artist_id' => $artist->id,
                'images' => [$image],
            ]);

            $response->assertRedirect(route('artwork.index'));
        }

        expect(Artwork::count())->toBe(count($formats));
    });
});

// ============================================
// RESOURCE TESTS
// ============================================

describe('Resource', function () {
    beforeEach(function () {
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    test('artwork resource includes images data', function () {
        $artist = Artist::factory()->create();
        $artwork = Artwork::factory()->create([
            'artist_id' => $artist->id,
        ]);

        $image = UploadedFile::fake()->image('test.jpg');
        $artwork->addMedia($image)->toMediaCollection('images');

        $response = $this->get(route('artwork.edit', $artwork));

        $response->assertInertia(fn ($page) => $page
            ->component('artwork/Form')
            ->has('artwork.images', 1)
            ->where('artwork.images.0.id', $artwork->getFirstMedia('images')->id)
        );
    });
});
