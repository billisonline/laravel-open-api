<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiParameter;
use Illuminate\Support\Traits\Tappable;

class OpenApiParameterBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $in;

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function inPath()
    {
        $this->in = 'path';

        return $this;
    }

    public function build()
    {
        return new OpenApiParameter([
            'name' => $this->name,
            'in' => $this->in,
        ]);
    }
}