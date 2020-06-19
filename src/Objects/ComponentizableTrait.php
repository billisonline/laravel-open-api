<?php

namespace BYanelli\OpenApiLaravel\Objects;

/**
 * @property string $componentType
 */
trait ComponentizableTrait
{
    /**
     * @var string
     */
    protected $componentKey;

    /**
     * @var string
     */
    protected $componentTitle;

    protected function hasComponentKey(): bool
    {
        return !empty($this->componentKey);
    }

    public function componentKey(string $componentKey): self
    {
        $this->componentKey = $componentKey;

        return $this;
    }

    protected function componentTitle(string $componentTitle): self
    {
        $this->componentTitle = $componentTitle;

        return $this;
    }

    public function getComponentKey(): string
    {
        return $this->componentKey;
    }

    protected function getComponentTitle(): string
    {
        return $this->componentTitle;
    }

    public function getComponentType(): string
    {
        return $this->componentType;
    }

    public function hasComponentKeyAndTitle(): bool
    {
        return !empty($this->componentKey) && !empty($this->componentTitle);
    }
}