<?php

namespace BYanelli\OpenApiLaravel\Builders;

trait InteractsWithCurrentDefinition
{
    /**
     * @var OpenApiDefinitionBuilder|null
     */
    private $currentDefinition;

    protected function saveCurrentDefinition(): void
    {
        $this->currentDefinition = OpenApiDefinitionBuilder::getCurrent();
    }

    protected function inDefinitionContext(): bool
    {
        return !is_null($this->currentDefinition);
    }
}