<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Validator;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function createAndIndex()
    {
        /* set validation rules */
        $indexResponseRules = [
            /* data */
            'data' => 'required|array',
            'data.*.id' => 'required',
            'data.*.content' => 'required',
            'data.*.created_at' => 'required',
            'data.*.created_at.date' => 'required|date',
            'data.*.created_at.timezone_type' => 'required|int',
            'data.*.created_at.timezone' => 'required|string',

            /* links */
            'links' => 'required',
            'links.first' => 'required|string',
            'links.last' => 'required|string',
            'links.prev' => 'string|nullable',
            'links.next' => 'string|nullable',

            /* meta */
            'meta' => 'required',
            'meta.current_page' => 'required|int',
            'meta.from' => 'required|int',
            'meta.last_page' => 'required|int',
            'meta.path' => 'required|string',
            'meta.per_page' => 'required|int',
            'meta.to' => 'required|int',
            'meta.total' => 'required|int',
        ];

        $createResponseRules = [
            'data' => 'required',
            'data.id' => 'required',
            'data.content' => 'required',
            'data.created_at' => 'required',
            'data.created_at.date' => 'required|date',
            'data.created_at.timezone_type' => 'required|int',
            'data.created_at.timezone' => 'required|string',
        ];

        /* create a post */
        $response = $this->call('POST', 'api/v1/posts', [
            'content' => 'This is a post request post content.'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $response = json_decode($response->getContent(), true);

        $validator = Validator::make($response, $createResponseRules);

        $this->assertFalse($validator->fails());

        /* get posts */
        $response = $this->get('api/v1/posts');

        $response->assertOk();

        $response = json_decode($response->getContent(), true);

        $validator = Validator::make($response, $indexResponseRules);

        $this->assertFalse($validator->fails());
    }
}
