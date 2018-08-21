<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Validator;

class PostControllerTest extends TestCase
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
     * @group Posts
     */
    public function createPostAndIndex()
    {
        $data = [
            'content' => 'test create post',
        ];

        /* Create a post */
        $this->post('api/v1/posts', $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('posts', $data);

        /* Index posts */
        $response = $this->get('api/v1/posts')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment($data)
            ->json();

        $indexResponseRules = [
            /* data */
            'data'                            => 'required|array',
            'data.*.id'                       => 'required',
            'data.*.content'                  => 'required',
            'data.*.created_at'               => 'required',
            'data.*.created_at.date'          => 'required|date',
            'data.*.created_at.timezone_type' => 'required|int',
            'data.*.created_at.timezone'      => 'required|string',

            /* links */
            'links'       => 'required',
            'links.first' => 'required|string',
            'links.last'  => 'required|string',
            'links.prev'  => 'string|nullable',
            'links.next'  => 'string|nullable',

            /* meta */
            'meta'              => 'required',
            'meta.current_page' => 'required|int',
            'meta.from'         => 'required|int',
            'meta.last_page'    => 'required|int',
            'meta.path'         => 'required|string',
            'meta.per_page'     => 'required|int',
            'meta.to'           => 'required|int',
            'meta.total'        => 'required|int',
        ];

        $validator = Validator::make($response, $indexResponseRules);

        $this->assertFalse($validator->fails());
    }

    /**
     * @test
     * @group Posts
     */
    public function createPostWithImage()
    {
        /* Upload an image */
        $image = UploadedFile::fake()->image('fake.image.jpg');

        $response = $this->post('api/v1/images', ['image' => $image])
            ->assertStatus(Response::HTTP_CREATED)->json();

        /* Create a post with image */
        $data = [
            'content' => 'create post with image test',
            'imageId' => $response['id'],
        ];

        $response = $this->post('api/v1/posts', $data)
            ->assertStatus(Response::HTTP_CREATED)->json();

        $createResponseRules = [
            'data'                          => 'required',
            'data.id'                       => 'required',
            'data.content'                  => 'required',
            'data.image'                    => 'required|url',
            'data.created_at'               => 'required',
            'data.created_at.date'          => 'required|date',
            'data.created_at.timezone_type' => 'required|int',
            'data.created_at.timezone'      => 'required|string',
        ];

        $validator = Validator::make($response, $createResponseRules);

        $this->assertFalse($validator->fails());

        $this->assertDatabaseHas('posts', ['content' => $data['content']]);
    }

    /**
     * @test
     * @group Posts
     */
    public function createPostWithUsedImage()
    {
        /* Upload an image */
        $image = UploadedFile::fake()->image('fake.image.jpg');

        $response = $this->post('api/v1/images', ['image' => $image])
            ->assertStatus(Response::HTTP_CREATED)->json();

        /* Create a post with image */
        $data = [
            'content' => 'create post with image test',
            'imageId' => $response['id'],
        ];

        $this->post('api/v1/posts', $data)->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('posts', ['content' => $data['content']]);

        /* Create another post using the same image */
        $failedPostData = [
            'content' => 'this post should not success',
            'imageId' => $response['id'],
        ];

        $this->post('api/v1/posts', $failedPostData)->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertDatabaseMissing('posts', ['content' => $failedPostData['content']]);
    }

    /**
     * @test
     * @group Posts
     */
    public function createPostWithMissedImage()
    {
        /* Create a post with a non-uploaded image id */
        $data = [
            'content' => 'create post with missing image test',
            'imageId' => 1,
        ];

        $this->post('api/v1/posts', $data)->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertDatabaseMissing('posts', ['content' => $data['content']]);
    }
}
