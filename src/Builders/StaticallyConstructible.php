<?php

namespace BYanelli\OpenApiLaravel\Builders;

trait StaticallyConstructible
{
    public static function make()
    {
        return new static;
    }
}