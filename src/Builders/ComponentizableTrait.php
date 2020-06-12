<?php

namespace BYanelli\OpenApiLaravel\Builders;

/**
 * @property string $componentType
 */
trait ComponentizableTrait
{
    /**
     * @var string
     */
    protected $componentName;

    protected function hasComponentName(): bool
    {
        return !empty($this->componentName);
    }

    public function refName(string $refName): self
    {
        $this->componentName = $refName;

        return $this;
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }
}