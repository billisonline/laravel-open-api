<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\OpenApiSchema;

class ResponseProperties
{
    use PerDefinitionPerObjectSingleton;

    /**
     * @var string
     */
    private $response;

    /**
     * @var OpenApiSchema
     */
    private $schema = null;

    public static function for(string $response)
    {
        if ($definition = OpenApiDefinition::current()) {
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
     * @param OpenApiSchema|array $schema
     * @return $this
     */
    public function schema($schema): self
    {
        if (is_array($schema)) {$schema = OpenApiSchema::fromArray($schema);}

        $this->schema = $schema;

        return $this;
    }

    public function getSchema(): ?OpenApiSchema
    {
        return $this->schema;
    }
}