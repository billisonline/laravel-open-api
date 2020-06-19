<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use BYanelli\OpenApiLaravel\Objects\OpenApiSchema;

class FormRequestProperties
{
    use PerDefinitionPerObjectSingleton;

    /**
     * @var string
     */
    private $request;

    /**
     * @var OpenApiSchema
     */
    private $schema;

    public function __construct(string $request)
    {
        $this->request = $request;
    }


    /**
     * @param OpenApiSchema|array $schema
     * @return $this
     */
    public function setSchema($schema): self
    {
        if (is_array($schema)) {$schema = OpenApiSchema::fromArray($schema);}

        $this->schema = $schema;

        return $this;
    }

    public function schema(): ?OpenApiSchema
    {
        if ($this->schema) {
            return clone $this->schema;
        }

        return null;
    }
}