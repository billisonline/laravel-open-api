<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Support\Model;
use Illuminate\Support\Traits\Tappable;

class OpenApiResponseBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var int
     */
    private $status;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $jsonSchema;

    public function fromResource(string $resource, string $model): self
    {
        $this->status(200);

        $wrappedResource = new JsonResource($resource, new Model(new $model)); //todo: wrap here or pass class names to schema builder?

        return $this->jsonSchema(OpenApiSchemaBuilder::make()->fromResource($wrappedResource));
    }

    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function jsonSchema(OpenApiSchemaBuilder $jsonSchema): self
    {
        $this->jsonSchema = $jsonSchema;
        
        return $this;
    }

    public function build()
    {
        return new OpenApiResponse([
            'status'        => $this->status,
            'jsonSchema'    => $this->jsonSchema->build(),
        ]);
    }
}