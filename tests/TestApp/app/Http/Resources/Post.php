<?php

namespace TestApp\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'conditional' => $this->when(false, function () {return $this->secret;}),
            'headlineSlug' => $this->headline_slug,
            'author' => new User($this->author),
        ];
    }
}