<?php

namespace BYanelli\OpenApiLaravel\Builders;

interface ResponseSchemaWrapper
{
    public function wrap(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder;
}