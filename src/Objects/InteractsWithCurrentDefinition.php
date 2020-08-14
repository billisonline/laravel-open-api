<?php

namespace BYanelli\OpenApiLaravel\Objects;

trait InteractsWithCurrentDefinition
{
    /**
     * @var OpenApiDefinition|null
     */
    protected $currentDefinition;

    protected function saveCurrentDefinition(): void
    {
        $this->currentDefinition = OpenApiDefinition::current();
    }

    protected function inDefinitionContext(): bool
    {
        return !is_null($this->currentDefinition);
    }

    public function whenInDefinitionContext(callable $callable)
    {
        if ($currentDefinition = ($this->currentDefinition ?: OpenApiDefinition::current())) {
            $callable($currentDefinition);
        }
    }
}