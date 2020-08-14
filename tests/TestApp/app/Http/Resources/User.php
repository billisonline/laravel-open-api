<?php

namespace TestApp\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'posts' => $this->posts->mapInto(Post::class),
        ];
    }
}