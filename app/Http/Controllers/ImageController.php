<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageCreateRequest;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    /** @var string POST_IMAGE_STORAGE_PATH: the image storage path */
    private const POST_IMAGE_STORAGE_PATH = 'images';

    /**
     * @param Image $image
     *
     * @return mixed
     */
    public function show(Image $image)
    {
        $imagePath = storage_path('app' . DIRECTORY_SEPARATOR . $image->image_location);

        return response()->file($imagePath);
    }

    /**
     * @param ImageCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(ImageCreateRequest $request): JsonResponse
    {
        $image = DB::transaction(function () use ($request) {
            $fileName = time() . '.' . $request->image->getClientOriginalName();

            $request->file('image')->storeAs(self::POST_IMAGE_STORAGE_PATH, $fileName);

            $image = Image::create([
                'image_location' => self::POST_IMAGE_STORAGE_PATH . DIRECTORY_SEPARATOR . $fileName,
            ]);

            return $image;
        });

        return response()->json([
            'id' => $image->id,
        ], Response::HTTP_CREATED);
    }
}
