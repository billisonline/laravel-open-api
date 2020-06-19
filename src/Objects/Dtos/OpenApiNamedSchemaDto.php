<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

class OpenApiNamedSchemaDto extends OpenApiSchemaDto
{
    /** @var string */
    public $name;

    protected $exceptKeys = ['name', 'componentKey'];
}