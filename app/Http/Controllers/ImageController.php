<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageCreateRequest;
use App\Models\Image;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /** @var string POST_IMAGE_STORAGE_PATH: the image storage path */
    private const POST_IMAGE_STORAGE_PATH = 'treehole/images';

    /** @var Filesystem $fileSystem */
    private $fileSystem;

    /**
     * ImageController constructor.
     */
    public function __construct()
    {
        $storageDisk = config('filesystems.default');

        $this->fileSystem = Storage::disk($storageDisk);
    }

    /**
     * @param Image $image
     *
     * @return mixed
     */
    public function show(Image $image)
    {
        /*
         * TODO:
         *
         * A more appropriate response is the following but it is not supported since the image is not public accessable
         *
         * $url = $this->fileSystem->url($image->image_location);
         * return response()->download($url);
         */

        $image = $this->fileSystem->get($image->image_location);

        return response()->make($image, Response::HTTP_OK, [
            'Content-Type' => 'image/png',
        ]);
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

            $image = Image::create([
                'image_location' => self::POST_IMAGE_STORAGE_PATH . DIRECTORY_SEPARATOR . $fileName,
            ]);

            $this->fileSystem->putFileAs(self::POST_IMAGE_STORAGE_PATH, $request->file('image'), $fileName);

            return $image;
        });

        return response()->json([
            'id' => $image->id,
        ], Response::HTTP_CREATED);
    }
}
