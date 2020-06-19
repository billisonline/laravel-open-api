<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use BYanelli\OpenApiLaravel\Objects\OpenApiSchema;

class JsonResourceProperties
{
    use PerDefinitionPerObjectSingleton;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var string|null
     */
    private $model = null;

    /**
     * @var OpenApiSchema|null
     */
    private $schema = null;


    protected function __construct(string $resource)
    {
        $this->resource = $resource;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function model(): ?string
    {
        return $this->model;
    }

    public function modelInstance(): ?Model
    {
        $model = $this->model;

        return is_null($model)? null: new Model(new $model);
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
        return $this->schema;
    }
}