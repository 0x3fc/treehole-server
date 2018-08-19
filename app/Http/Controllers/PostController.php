<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\PostCreateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /** @var int INDEX_PER_PAGE: the default paginate per page property */
    private const INDEX_PER_PAGE = 20;

    /** @var string POST_IMAGE_STORAGE_PATH: the image storage path */
    private const POST_IMAGE_STORAGE_PATH = 'postImages';

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
        $post = DB::transaction(function () use ($request) {
            $post = Post::create([
                'content' => $request->input('content'),
            ]);

            if ($image = $request->image) {
                $fileName = $post->id . '.' . $image->getClientOriginalName();

                $request->file('image')->storeAs(self::POST_IMAGE_STORAGE_PATH, $fileName);

                $post->image = self::POST_IMAGE_STORAGE_PATH . DIRECTORY_SEPARATOR . $fileName;

                $post->save();
            }

            return $post;
        });

        return PostResource::make($post)->response();
    }
}
