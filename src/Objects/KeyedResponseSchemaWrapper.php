<?php

namespace BYanelli\OpenApiLaravel\Objects;

class KeyedResponseSchemaWrapper implements ResponseSchemaWrapper
{
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function wrap(OpenApiSchema $schema): OpenApiSchema
    {
        return OpenApiSchema::make()
            ->type('object')
            ->addProperty($schema->name($this->key));
    }
}