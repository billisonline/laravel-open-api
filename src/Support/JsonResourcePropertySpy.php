<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Http\Resources\Json\JsonResource;

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

    public function __construct(string $accessor)
    {
        $this->accessors[] = $accessor;
    }

    public function accessor(): string
    {
        return $this->accessors[0];
    }

    /*public function __call($name, $arguments)
    {
        if (
            ($name == 'mapInto')
            && (!is_null($firstArg = Arr::first($arguments)))
            && ($this->isJsonResource($firstArg))
        ) {
            $this->resourceArrayType = $firstArg;
        }
    }

    public function isResourceArray(): bool
    {
        return !is_null($this->resourceArrayType);
    }

    protected function isJsonResource($firstArg): bool
    {
        return class_exists($firstArg) && is_subclass_of($firstArg, JsonResource::class);
    }*/

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