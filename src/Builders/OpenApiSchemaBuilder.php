<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiNamedSchema;
use BYanelli\OpenApiLaravel\OpenApiNamedSchemaRef;
use BYanelli\OpenApiLaravel\OpenApiSchema;
use BYanelli\OpenApiLaravel\OpenApiSchemaRef;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Support\Model;
use Illuminate\Support\Traits\Tappable;

class OpenApiSchemaBuilder
{
    use StaticallyConstructible, Tappable;

    /**
     * @var string
     */
    private $type;

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
     * @var string
     */
    private $ref = '';

    public function fromResource(JsonResource $resource): self
    {
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

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function object(array $properties): self
    {
        $this->type('object');

        foreach ($properties as $name => $type) {
            $this->addProperty(
                self::make()->name($name)->type($type)
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

    public function ref(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function build()
    {
        if (!empty($this->ref) && !empty($this->name)) {
            return new OpenApiNamedSchemaRef([
                'name'  => $this->name,
                'ref'   => $this->ref
            ]);
        }

        if (!empty($this->ref)) {
            return new OpenApiSchemaRef(['ref' => $this->ref]);
        }

        $type = empty($this->name) ? OpenApiSchema::class : OpenApiNamedSchema::class;

        $params = [
            'type'      => $this->type,
            'nullable'  => $this->nullable,
            'properties' => collect($this->properties)->map->build()->all(),
        ];

        if (!empty($this->name)) {$params['name'] = $this->name;}

        return new $type($params);
    }
}