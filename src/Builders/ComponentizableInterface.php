<?php

namespace BYanelli\OpenApiLaravel\Builders;

interface ComponentizableInterface
{
    public function getComponentType(): string;

    public function getComponentName(): string;

    public function getComponentObject();
}