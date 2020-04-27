<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var array
     */
    protected $properties;

    public function __construct(string $className, Model $model)
    {
        if (!is_subclass_of($className, JsonResource::class)) {
            throw new \Exception;
        }

        $this->className = $className;
        $this->model = $model;
        $this->properties = $this->getProperties($className);
    }

    protected function getProperties(string $className)
    {
        $spy = new ResourceSpy();

        /** @var JsonResource $instance */
        /** @noinspection ALL */
        $instance = eval('return new class ($spy) extends '.$className.' { use \BYanelli\OpenApiLaravel\Support\AlwaysEvaluatesConditionalProperties; };');

        return $instance->toArray(Request::createFromGlobals());
    }

    public function propertyNames()
    {
        return array_keys($this->properties);
    }

    public function propertyType(string $name)
    {
        $property = $this->properties[$name];

        if ($this->model->hasColumn($property)) {
            return $this->model->getAttributeType($property);
        }

        throw new \Exception;
    }
}