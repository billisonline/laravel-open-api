<?php


namespace BYanelli\OpenApiLaravel\LaravelReflection;


class JsonResourceSpy
{
    public function __get($name)
    {
        return new JsonResourcePropertySpy($name);
    }
}