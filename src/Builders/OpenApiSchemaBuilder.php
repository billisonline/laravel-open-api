<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiNamedSchema;
use BYanelli\OpenApiLaravel\OpenApiNamedSchemaRef;
use BYanelli\OpenApiLaravel\OpenApiSchema;
use BYanelli\OpenApiLaravel\OpenApiSchemaRef;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Support\Tappable;
use Illuminate\Support\Str;

class OpenApiSchemaBuilder implements ComponentizableInterface
{
    use StaticallyConstructible, Tappable, ComponentizableTrait;

    /**
     * @var string
     */
    private $type;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $items;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var OpenApiDefinitionBuilder|null
     */
    private $currentDefinition;

    public $componentType = OpenApiDefinitionBuilder::COMPONENT_TYPE_SCHEMA;

    public function __construct()
    {
        $this->currentDefinition = OpenApiDefinitionBuilder::getCurrent();
    }

    public static function fromArray(array $array): self
    {
        return (new static)->object($array);
    }

    public function fromResource(JsonResource $resource): self
    {
        if ($schema = $resource->definedProperties()->schema()) {
            return $schema->refName($resource->refName());
        }

        $this->type('object');

        foreach ($resource->properties() as $property) {
            if ($property->type() == 'json_resource') {continue;} //todo: refs

            $this->addProperty(
                OpenApiSchemaBuilder::make()
                    ->name($property->name())
                    ->type($property->type())
                    ->nullable($property->isConditional())
            );
        }

        $this->refName($resource->refName());

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    private function parseName(string $name): array
    {
        if (Str::endsWith($name, '[]')) {
            return [str_replace('[]', '', $name), true];
        }

        return [$name, false];
    }

    private function parseType($type): array
    {
        if (is_array($type)) {
            return ['object', $type];
        }

        return [$type, []];
    }

    public function object(array $properties): self
    {
        return $this->type('object')->addProperties($properties);
    }

    public function addProperties(array $properties): self
    {
        /** @var string $name */
        /** @var string $type */
        foreach ($properties as $name => $type) {
            /** @var bool $isArray */
            /** @var array $objectProperties */
            [$name, $isArray] = $this->parseName($name);
            [$type, $objectProperties] = $this->parseType($type);

            $this->addProperty(
                self::make()
                    ->name($name)
                    ->when($isArray, function (self $property) use ($type, $objectProperties) {
                        $property
                            ->type('array')
                            ->items(
                                self::make()
                                    ->type($type)
                                    ->addProperties($objectProperties)
                            );
                    })
                    ->unless($isArray, function (self $property) use ($type, $objectProperties) {
                        $property
                            ->type($type)
                            ->addProperties($objectProperties);
                    })
            );
        }

        return $this;
    }

    public function nullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function addProperty(OpenApiSchemaBuilder $property): self
    {
        $this->properties[] = $property;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function items(OpenApiSchemaBuilder $items): self
    {
        $this->items = $items;

        return $this;
    }

    private function inDefinitionContext(): bool
    {
        return !is_null($this->currentDefinition);
    }

    private function hasName(): bool
    {
        return !empty($this->name);
    }

    private function buildSchema(array $overrides=[]): OpenApiSchema
    {
        $params = [
            'type'      => $this->type,
            'items'     => optional($this->items)->build(),
            'nullable'  => $this->nullable,
            'properties' => collect($this->properties)->map->build()->all(),
        ];

        if (!empty($this->name)) {$params['name'] = $this->name;}

        $params = array_merge($params, $overrides);

        $type = isset($params['name']) ? OpenApiNamedSchema::class : OpenApiSchema::class;

        return new $type($params);
    }

    public function getComponentObject()
    {
        return $this->buildSchema(['componentName' => $this->componentName]);
    }

    public function build()
    {
        if ($this->inDefinitionContext() && $this->hasComponentName() && $this->hasName()) {
            $this->currentDefinition->registerComponent($this);

            return new OpenApiNamedSchemaRef([
                'name'  => $this->name,
                'ref'   => $this->currentDefinition->refPath($this),
            ]);
        }

        if ($this->inDefinitionContext() && $this->hasComponentName()) {
            $this->currentDefinition->registerComponent($this);

            return new OpenApiSchemaRef([
                'ref' => $this->currentDefinition->refPath($this),
            ]);
        }

        return $this->buildSchema();
    }
}