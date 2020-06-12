<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\OpenApiResponseRef;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Support\ResponseProperties;
use Illuminate\Support\Traits\Tappable;

class OpenApiResponseBuilder implements ComponentizableInterface
{
    use Tappable,
        StaticallyConstructible,
        ComponentizableTrait,
        InteractsWithCurrentDefinition;

    /**
     * @var int
     */
    private $status;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $jsonSchema;

    private $description = 'Success'; //todo

    public $componentType = OpenApiDefinitionBuilder::COMPONENT_TYPE_RESPONSE;

    /**
     * @var OpenApiDefinitionBuilder
     */
    private $currentDefinition;

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

        $schema = OpenApiSchemaBuilder::make()->fromResource($resource);

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

        if ($schema = ResponseProperties::for($response)->schema()) {
            //todo: ref

            $this->jsonSchema($schema);
        }

        $this->componentName = class_basename($response);

        return $this;
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

    protected function wrap(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder
    {
        if ($this->currentDefinition) {
            $schema = $this->currentDefinition->wrapResponseSchema($schema);
        }

        return $schema;
    }

    public function getComponentObject()
    {
        return $this->buildSchema(['componentName' => $this->componentName]);
    }

    private function buildSchema(array $overrides=[])
    {
        return new OpenApiResponse(array_merge([
            'status'        => $this->status,
            'description'   => $this->description,
            'jsonSchema'    => $this->wrap($this->pluralize($this->jsonSchema))->build(),
        ], $overrides));
    }

    public function build()
    {
        if ($this->inDefinitionContext() && $this->hasComponentName()) {
            $this->currentDefinition->registerComponent($this);

            return new OpenApiResponseRef([
                'status' => $this->status,
                'ref' => $this->currentDefinition->refPath($this)
            ]);
        }

        return $this->buildSchema();
    }

    public function getStatus()
    {
        return $this->status;
    }

    private function pluralize(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder
    {
        return $this->isPlural? OpenApiSchemaBuilder::make()->type('array')->items($schema) : $schema;
    }
}