<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiPath;
use Illuminate\Support\Traits\Tappable;

class OpenApiPathBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $path;

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function build(): OpenApiPath
    {
        return new OpenApiPath(['path' => $this->path]);
    }
}