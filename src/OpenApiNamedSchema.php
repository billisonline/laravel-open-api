<?php

namespace BYanelli\OpenApiLaravel;

class OpenApiNamedSchema extends OpenApiSchema
{
    /** @var string */
    public $name;

    protected $exceptKeys = ['name', 'componentName'];
}