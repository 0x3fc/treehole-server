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

        Storage::fake('local');
    }

    /**
     * @test
     * @group Images
     */
    public function uploadImage()
    {
        $image = UploadedFile::fake()->image('fake.image.jpg');

        $response = $this->post('api/v1/images', ['image' => $image])
            ->assertStatus(Response::HTTP_CREATED)->json();

        $image = Image::findOrFail($response['id']);

        Storage::assertExists($image->image_location);

        // TODO: Show image is not able to test since the image is stored in fake storage but api gets the local storage
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
