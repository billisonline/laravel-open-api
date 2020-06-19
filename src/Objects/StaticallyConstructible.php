<?php

namespace BYanelli\OpenApiLaravel\Objects;

trait StaticallyConstructible
{
    public static function make()
    {
        return new static;
    }
}