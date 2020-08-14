<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDtoRef;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDtoRef;

class OpenApiSchemaLazyResourceRef extends OpenApiSchema
{
    /**
     * @var JsonResource
     */
    private $resource;

    public function fromResource(JsonResource $resource, array $resourceTypesVisited = []): OpenApiSchema
    {
        $this->resource = $resource;

        return $this;
    }

    public function getComponentKey(): string
    {
        return $this->resource->componentKey();
    }

    public function getComponentType(): string
    {
        return OpenApiDefinition::COMPONENT_TYPE_SCHEMA;
    }

    public function build()
    {
        $ref = (
            $this->inDefinitionContext()
                ? $this->currentDefinition->refPath($this)
                : '#/components/schemas/whatever' //todo
        );

        if (empty($this->name)) {
            return new OpenApiSchemaDtoRef(['ref' => $ref]);
        }

        return new OpenApiNamedSchemaDtoRef([
            'name'  => $this->name,
            'ref'   => $ref,
        ]);
    }
}