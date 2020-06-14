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
    protected $componentKey;

    protected function hasComponentKey(): bool
    {
        return !empty($this->componentKey);
    }

    public function componentKey(string $refName): self
    {
        $this->componentKey = $refName;

        return $this;
    }

    public function getComponentKey(): string
    {
        return $this->componentKey;
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }
}