<?php

namespace TestApp\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->when(false, function () {return $this->body;}),
            'headlineSlug' => $this->headline_slug,
            'author' => new User($this->author),
        ];
    }
}