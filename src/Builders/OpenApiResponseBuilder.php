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

    /**
     * @param JsonResource|string $resource
     * @return $this
     * @throws \Exception
     */
    public function fromResource($resource): self
    {
        $this->status(200);

        if (is_string($resource)) {
            $resource = new JsonResource($resource);
        }

        if (!($resource instanceof JsonResource)) {
            throw new \Exception;
        }

        $schema = OpenApiSchemaBuilder::make()->fromResource($resource);

        // If we're in a definition context, register the resource as a schema and use the "ref" in this response
        if ($definition = OpenApiDefinitionBuilder::getCurrent()) {
            $definition->registerResourceSchema($resource, $schema);

            return $this->jsonSchema($definition->getSchemaRefForResource($resource));
        }

        return $this->jsonSchema($schema);
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