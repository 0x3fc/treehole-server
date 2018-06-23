<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\PostIndexRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /** @var int DEFAULT_PER_PAGE: the default paginate per page property */
    private const DEFAULT_PER_PAGE = 20;

    /**
     * @param PostIndexRequest $request
     * @return JsonResponse: paginated post list ordered by the latest created date first
     */
    public function index(PostIndexRequest $request): JsonResponse
    {
        $perPage = $request->has('per_page') ? $request->per_page : self::DEFAULT_PER_PAGE;

        $posts = Post::orderBy('id', 'desc')->paginate($perPage);

        return PostResource::collection($posts)->response();
    }
}
