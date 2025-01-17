<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiResponseDto;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use BYanelli\OpenApiLaravel\LaravelReflection\Response;
use Illuminate\Support\Traits\Tappable;

class OpenApiResponse
{
    use Tappable, StaticallyConstructible, InteractsWithCurrentDefinition;

    /**
     * @var int
     */
    private $status;

    /**
     * @var OpenApiSchema
     */
    private $jsonSchema;

    private $description = 'Success'; //todo

    private $isPlural = false;

    public function __construct()
    {
        $this->saveCurrentDefinition();
    }

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

        $schema = OpenApiSchema::make()->fromResource($resource);

        return $this->jsonSchema($schema);
    }

    public function plural()
    {
        $this->isPlural = true;

        return $this;
    }

    public function fromResponse(string $response, int $status=200): self
    {
        $this->status($status);

        $response = new Response($response);

        if ($schema = $response->schema()) {
            $schema
                ->setComponentKey($response->componentKey())
                ->setComponentTitle($response->componentTitle());

            $this->jsonSchema($schema);
        }

        return $this;
    }

    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function jsonSchema(OpenApiSchema $jsonSchema): self
    {
        $this->jsonSchema = $jsonSchema;
        
        return $this;
    }

    protected function wrap(OpenApiSchema $schema): OpenApiSchema
    {
        if ($this->currentDefinition) {
            $schema = $this->currentDefinition->wrapResponseSchema($schema);
        }

        return $schema;
    }

    public function build()
    {
        return new OpenApiResponseDto([
            'status'        => $this->status,
            'description'   => $this->description,
            'jsonSchema'    => (
            !is_null($this->jsonSchema)
                ? $this->wrap($this->pluralize($this->jsonSchema))->build()
                : null
            ),
        ]);
    }

    public function getStatus()
    {
        return $this->status;
    }

    private function pluralize(OpenApiSchema $schema): OpenApiSchema
    {
        return $this->isPlural? OpenApiSchema::make()->type('array')->items($schema) : $schema;
    }
}