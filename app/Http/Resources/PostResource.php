<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'content'    => $this->content,
            'image'      => $this->image ? action('ImageController@show', ['image' => $this->image]) : null,
            'created_at' => $this->created_at,
        ];
    }
}
