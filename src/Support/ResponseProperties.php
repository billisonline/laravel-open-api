<?php

namespace BYanelli\OpenApiLaravel\Support;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;

class ResponseProperties
{
    use PerDefinitionPerObjectSingleton;

    /**
     * @var string
     */
    private $response;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $schema = null;

    public static function for(string $response)
    {
        if ($definition = OpenApiDefinitionBuilder::getCurrent()) {
            if ($existingInstance = $definition->getPropertiesInstance(static::class, $response)) {
                return $existingInstance;
            }

            $definition->setPropertiesInstance(static::class, $response, $newInstance = new static($response));

            return $newInstance;
        }

        return new static($response);
    }

    protected function __construct(string $response)
    {
        $this->response = $response;
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