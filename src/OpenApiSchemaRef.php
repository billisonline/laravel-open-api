<?php

namespace BYanelli\OpenApiLaravel;

class OpenApiSchemaRef extends OpenApiSchema
{
    /** @var string */
    public $ref;

    public function __construct(array $parameters = [])
    {
        $this->ref = $parameters['ref'] ?? null;
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