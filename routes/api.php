<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    /* Posts */
    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', 'PostController@index');
        Route::post('/', 'PostController@store');
    });

    /* Images */
    Route::group(['prefix' => 'images'], function () {
        Route::get('/{image}', 'ImageController@show');
        Route::post('/', 'ImageController@store');
    });
});
