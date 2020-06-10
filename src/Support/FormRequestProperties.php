<?php

namespace BYanelli\OpenApiLaravel\Support;

use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;

class FormRequestProperties
{
    use PerDefinitionPerObjectSingleton;

    /**
     * @var string
     */
    private $request;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $schema;

    public function __construct(string $request)
    {
        $this->request = $request;
    }


    /**
     * @param OpenApiSchemaBuilder|array $schema
     * @return $this
     */
    public function setSchema($schema): self
    {
        if (is_array($schema)) {$schema = OpenApiSchemaBuilder::fromArray($schema);}

        $this->schema = $schema;

        return $this;
    }

    public function schema(): ?OpenApiSchemaBuilder
    {
        return $this->schema;
    }
}