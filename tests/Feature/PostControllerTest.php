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

        Storage::fake('local');
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

//    /**
//     * @test
//     */
//    public function createAndIndex()
//    {
//        /* set validation rules */
//        $indexResponseRules = [
//            /* data */
//            'data' => 'required|array',
//            'data.*.id' => 'required',
//            'data.*.content' => 'required',
//            'data.*.created_at' => 'required',
//            'data.*.created_at.date' => 'required|date',
//            'data.*.created_at.timezone_type' => 'required|int',
//            'data.*.created_at.timezone' => 'required|string',
//
//            /* links */
//            'links' => 'required',
//            'links.first' => 'required|string',
//            'links.last' => 'required|string',
//            'links.prev' => 'string|nullable',
//            'links.next' => 'string|nullable',
//
//            /* meta */
//            'meta' => 'required',
//            'meta.current_page' => 'required|int',
//            'meta.from' => 'required|int',
//            'meta.last_page' => 'required|int',
//            'meta.path' => 'required|string',
//            'meta.per_page' => 'required|int',
//            'meta.to' => 'required|int',
//            'meta.total' => 'required|int',
//        ];
//
//        $createResponseRules = [
//            'data' => 'required',
//            'data.id' => 'required',
//            'data.content' => 'required',
//            'data.created_at' => 'required',
//            'data.created_at.date' => 'required|date',
//            'data.created_at.timezone_type' => 'required|int',
//            'data.created_at.timezone' => 'required|string',
//        ];
//
//        /* create a post */
//        $response = $this->call('POST', 'api/v1/posts', [
//            'content' => 'This is a post request post content.'
//        ]);
//
//        $response->assertStatus(Response::HTTP_CREATED);
//
//        $response = json_decode($response->getContent(), true);
//
//        $validator = Validator::make($response, $createResponseRules);
//
//        $this->assertFalse($validator->fails());
//
//        /* get posts */
//        $response = $this->get('api/v1/posts');
//
//        $response->assertOk();
//
//        $response = json_decode($response->getContent(), true);
//
//        $validator = Validator::make($response, $indexResponseRules);
//
//        $this->assertFalse($validator->fails());
//    }
//
//    /**
//     * @test
//     */
//    public function storeImage()
//    {
//        $image = UploadedFile::fake()->image('fake.image.jpg');
//
//        $testContent = 'test content';
//
//        $this->post('api/v1/posts', [
//            'content' => $testContent,
//            'image'   => $image,
//        ])->assertStatus(Response::HTTP_CREATED);
//
//        $post = Post::where('content', $testContent)->first();
//
//        Storage::disk('local')->assertExists($post->image);
//
//        $response = $this->get("api/v1/posts/images/{$post->id}")
//            ->assertStatus(Response::HTTP_OK);
//    }
//
//    /**
//     * @test
//     */
//    public function retrieveNotFoundImage()
//    {
//        $testContent = 'test image not found';
//
//        $this->post('api/v1/posts', ['content' => $testContent])
//            ->assertStatus(Response::HTTP_CREATED);
//
//        $post = Post::where('content', $testContent)->first();
//
//        $this->get("api/v1/posts/images/{$post->id}")
//            ->assertStatus(Response::HTTP_NOT_FOUND);
//    }
}
