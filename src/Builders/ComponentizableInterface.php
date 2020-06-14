<?php

namespace BYanelli\OpenApiLaravel\Builders;

interface ComponentizableInterface
{
    public function getComponentType(): string;

    public function getComponentKey(): string;

    public function getComponentObject();
}