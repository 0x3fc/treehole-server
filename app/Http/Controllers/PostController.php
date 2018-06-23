<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\PostCreateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /** @var int INDEX_PER_PAGE: the default paginate per page property */
    private const INDEX_PER_PAGE = 20;

    /**
     * @return JsonResponse: paginated post list ordered by the latest created date first
     */
    public function index(): JsonResponse
    {
        $posts = Post::orderBy('id', 'desc')->paginate(self::INDEX_PER_PAGE);

        return PostResource::collection($posts)->response();
    }

    /**
     * @param PostCreateRequest $request
     * @return JsonResponse
     */
    public function create(PostCreateRequest $request): JsonResponse
    {
        Post::create(['content' => $request->input('content')]);

        return response()->json(['success' => true]);
    }
}
