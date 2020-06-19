<?php

namespace BYanelli\OpenApiLaravel\Objects;

interface ComponentizableInterface
{
    public function getComponentType(): string;

    public function getComponentKey(): string;

    public function getComponentObject();
}