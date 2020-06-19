<?php

namespace BYanelli\OpenApiLaravel\Objects;

trait InteractsWithCurrentDefinition
{
    /**
     * @var OpenApiDefinition|null
     */
    private $currentDefinition;

    protected function saveCurrentDefinition(): void
    {
        $this->currentDefinition = OpenApiDefinition::getCurrent();
    }

    protected function inDefinitionContext(): bool
    {
        return !is_null($this->currentDefinition);
    }
}