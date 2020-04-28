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

        $properties = $instance->toArray(Request::createFromGlobals());

        // Convert objects to their class names
        foreach ($properties as $key => $property) {
            if (is_object($property)) {
                $properties[$key] = get_class($property);
            }
        }

        return $properties;
    }

    public function propertyNames()
    {
        return array_keys($this->properties);
    }

    public function propertyType(string $name)
    {
        $property = $this->properties[$name];

        if (class_exists($property) && is_subclass_of($property, JsonResource::class)) {
            return $property;
        }

        if ($this->model->hasColumn($property)) {
            return $this->model->getColumnType($property);
        }

        if ($this->model->hasGetMutator($property)) {
            return $this->model->getGetMutatorType($property);
        }

        // if has $name method and it returns a relation:
        // - reflect model -> resource type
        // - determine whether resource is singular or plural

        throw new \Exception;
    }
}