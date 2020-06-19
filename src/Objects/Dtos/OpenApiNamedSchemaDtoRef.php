<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

class OpenApiNamedSchemaDtoRef extends OpenApiNamedSchemaDto
{
    /** @var string */
    public $ref;

    public function __construct(array $parameters = [])
    {
        $this->ref = $parameters['ref'] ?? null;
        $this->name = $parameters['name'] ?? null;
    }

    protected function getFieldValidators(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            '$ref' => $this->ref,
        ];
    }
}