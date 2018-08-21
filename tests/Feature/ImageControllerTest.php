<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up tests
     */
    public function setUp()
    {
        parent::setUp();

        $storageDisk = config('filesystems.default');

        Storage::fake($storageDisk);
    }

    /**
     * @test
     * @group Images
     */
    public function uploadImageAndShow()
    {
        $image = UploadedFile::fake()->image('fake.image.jpg');

        $response = $this->post('api/v1/images', ['image' => $image])
            ->assertStatus(Response::HTTP_CREATED)->json();

        $image = Image::findOrFail($response['id']);

        Storage::assertExists($image->image_location);

        $this->get("api/v1/images/{$response['id']}")
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     * @group Images
     */
    public function uploadImageWithWrongType()
    {
        $file = UploadedFile::fake()->create('fake.file.pdf');

        $this->post('api/v1/images', ['image' => $file])
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @group Images
     */
    public function uploadImageWithTooLargeSize()
    {
        $image = UploadedFile::fake()->create('fake.image.jpg', 600);

        $this->post('api/v1/images', ['image' => $image])
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     * @group Images
     */
    public function showNotFoundImage()
    {
        $this->get('api/v1/images/1')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
