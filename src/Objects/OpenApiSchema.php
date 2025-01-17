<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDtoRef;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDtoRef;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use BYanelli\OpenApiLaravel\Objects\Tappable;
use Illuminate\Support\Str;

class OpenApiSchema implements ComponentizableInterface
{
    use StaticallyConstructible,
        Tappable,
        ComponentizableTrait,
        InteractsWithCurrentDefinition;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var OpenApiSchema
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
    protected $name = '';

    /**
     * @var string
     */
    public $componentType = OpenApiDefinition::COMPONENT_TYPE_SCHEMA;

    /**
     * @var string
     */
    private $description;

    public static function asComponent(string $name, array $body=[]): self
    {
        return static::make()->component($name, $body);
    }

    public function __construct()
    {
        $this->saveCurrentDefinition();
    }

    public function setComponentKey(string $componentKey): self
    {
        $this->componentKey = $componentKey;

        return $this;
    }

    public function setComponentTitle(string $componentTitle): self
    {
        $this->componentTitle = $componentTitle;

        return $this;
    }

    public function component($name, array $body=[]): self
    {
        if (is_array($name)) {
            [$this->componentKey, $this->componentTitle] = $name;
        } elseif (is_string($name)) {
            [$this->componentKey, $this->componentTitle] = [$name, $name];
        }

        if ($body) {$this->object($body);}

        return $this;
    }

    public static function fromArray(array $array): self
    {
        return (new static)->object($array);
    }

    public function fromResource(JsonResource $resource, array $resourceTypesVisited=[]): self
    {
        if ($schema = $resource->definedProperties()->getSchema()) {
            return $schema->componentKey($resource->componentKey())->componentTitle($resource->componentTitle());
        }

        $this->type('object');

        $this->description($resource->description());

        foreach ($resource->properties() as $property) {
            if ($property->isJsonResourceArray()) {
                $itemResourceType = $property->itemResourceType();

                if (in_array($itemResourceType, $resourceTypesVisited)) {
                    $this->addProperty(
                        OpenApiSchema::make()
                            ->name($property->name())
                            ->type('array')
                            ->items(
                                OpenApiSchemaLazyResourceRef::make()
                                    ->fromResource(new JsonResource($itemResourceType))
                            )
                    );
                } else {
                    $this->addProperty(
                        OpenApiSchema::make()
                            ->name($property->name())
                            ->type('array')
                            ->items(
                                OpenApiSchema::make()
                                    ->fromResource(
                                        new JsonResource($itemResourceType),
                                        array_merge($resourceTypesVisited, [$itemResourceType])
                                    )
                            )
                    );
                }

                continue;
            }

            if ($property->isJsonResource()) {
                $resourceType = $property->resourceType();

                if (in_array($resourceType, $resourceTypesVisited)) {
                    $this->addProperty(
                        OpenApiSchemaLazyResourceRef::make()
                            ->name($property->name())
                            ->fromResource(new JsonResource($property->resourceType()))
                    );
                } else {
                    $this->addProperty(
                        OpenApiSchema::make()
                            ->name($property->name())
                            ->fromResource(
                                new JsonResource($property->resourceType()),
                                array_merge($resourceTypesVisited, [$resourceType])
                            )
                    );
                }

                continue;
            }

            $this->addProperty(
                OpenApiSchema::make()
                    ->name($property->name())
                    ->type($property->type())
                    ->description($property->description())
                    ->nullable($property->isConditional())
            );
        }

        $this->componentKey($resource->componentKey())->componentTitle($resource->componentTitle());

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

    public function addProperty(OpenApiSchema $property): self
    {
        $this->properties[] = $property;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function items(OpenApiSchema $items): self
    {
        $this->items = $items;

        return $this;
    }

    private function hasName(): bool
    {
        return !empty($this->name);
    }

    private function buildSchema(array $overrides=[]): OpenApiSchemaDto
    {
        $params = [
            'type'          => $this->type,
            'items'         => optional($this->items)->build(),
            'nullable'      => $this->nullable,
            'properties'    => collect($this->properties)->map->build()->all(),
            'description'   => $this->description,
        ];

        if (!empty($this->name)) {$params['name'] = $this->name;}

        $params = array_merge($params, $overrides);

        $type = isset($params['name']) ? OpenApiNamedSchemaDto::class : OpenApiSchemaDto::class;

        return new $type($params);
    }

    public function getComponentObject()
    {
        return $this->buildSchema([
            'componentKey'      => $this->componentKey,
            'componentTitle'    => $this->componentTitle,
        ]);
    }

    public function build()
    {
        if ($this->inDefinitionContext() && $this->hasComponentKey() && $this->hasName()) {
            $this->currentDefinition->registerComponent($this);

            return new OpenApiNamedSchemaDtoRef([
                'name'  => $this->name,
                'ref'   => $this->currentDefinition->refPath($this),
            ]);
        }

        if ($this->inDefinitionContext() && $this->hasComponentKey()) {
            $this->currentDefinition->registerComponent($this);

            return new OpenApiSchemaDtoRef([
                'ref' => $this->currentDefinition->refPath($this),
            ]);
        }

        return $this->buildSchema();
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}