<?php

namespace BYanelli\OpenApiLaravel\Support;

use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;

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
     * @var OpenApiSchemaBuilder|null
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

    public function setSchema(OpenApiSchemaBuilder $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function schema(): ?OpenApiSchemaBuilder
    {
        return $this->schema;
    }
}