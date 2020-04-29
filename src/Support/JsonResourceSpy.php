<?php


namespace BYanelli\OpenApiLaravel\Support;


class JsonResourceSpy
{
    public function __get($name)
    {
        return new JsonResourcePropertySpy($name);
    }
}