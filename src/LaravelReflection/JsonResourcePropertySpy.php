<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class JsonResourcePropertySpy
{
    /**
     * @var string[]|array
     */
    private $accessors;

    /**
     * @var bool
     */
    private $isConditional = false;

    /**
     * @var string|null
     */
    private $resourceArrayItemType = null;

    public function __construct(string $accessor)
    {
        $this->accessors[] = $accessor;
    }

    public function accessor(): string
    {
        return $this->accessors[0];
    }

    public function __call($name, $arguments)
    {
        if ($this->isMapIntoResourceCall($name, $arguments)) {
            $this->resourceArrayItemType = Arr::first($arguments);
        }

        return $this;
    }

    private function isMapIntoResourceCall($name, $arguments): bool
    {
        return (
            ($name == 'mapInto')
            && (!is_null($firstArg = Arr::first($arguments)))
            && ($this->isJsonResource($firstArg))
        );
    }

    protected function isJsonResource(string $class): bool
    {
        return class_exists($class) && is_subclass_of($class, JsonResource::class);
    }

    public function isResourceArray(): bool
    {
        return !is_null($this->resourceArrayItemType);
    }

    public function resourceArrayItemType(): string
    {
        return $this->resourceArrayItemType;
    }

    public function setIsConditional(bool $isConditional): self
    {
        $this->isConditional = $isConditional;

        return $this;
    }

    public function isConditional(): bool
    {
        return $this->isConditional;
    }
}