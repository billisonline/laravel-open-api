<?php


namespace BYanelli\OpenApiLaravel\Support;


class ResourceSpy
{
    public function __get($name)
    {
        return $name;
    }
}