<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\Support\JsonResource;
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

        // If we're in a definition context
        if ($definition = OpenApiDefinitionBuilder::getCurrent()) {
            // Register the resource as a schema
            $definition->registerResourceSchema($resource, $schema);

            // Use the "ref" instead of the full resource definition
            $resourceRef = $definition->getSchemaRefForResource($resource);

            return $this->jsonSchema($resourceRef);
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
        $this->jsonSchema = $this->wrap($jsonSchema);
        
        return $this;
    }

    protected function wrap(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder
    {
        if ($definition = OpenApiDefinitionBuilder::getCurrent()) {
            $schema = $definition->wrapResponseSchema($schema);
        }

        return $schema;
    }

    public function build()
    {
        return new OpenApiResponse([
            'status'        => $this->status,
            'jsonSchema'    => $this->jsonSchema->build(),
        ]);
    }
}