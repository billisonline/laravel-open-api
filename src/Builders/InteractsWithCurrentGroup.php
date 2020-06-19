<?php

namespace BYanelli\OpenApiLaravel\Builders;

/**
 * @mixin OpenApiOperationBuilder
 */
trait InteractsWithCurrentGroup
{
    /**
     * @var OpenApiGroup
     */
    protected $currentGroup;

    protected function saveCurrentGroup()
    {
        $this->currentGroup = OpenApiGroup::current();
    }

    protected function applyGroupProperties()
    {
        if ($this->currentGroup) {
            $this->currentGroup->apply($this);
        }
    }
}