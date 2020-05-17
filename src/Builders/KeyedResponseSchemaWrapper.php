<?php

namespace BYanelli\OpenApiLaravel\Builders;

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

    public function wrap(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder
    {
        return OpenApiSchemaBuilder::make()
            ->type('object')
            ->addProperty($schema->name($this->key));
    }
}