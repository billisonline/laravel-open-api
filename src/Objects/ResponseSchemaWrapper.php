<?php

namespace BYanelli\OpenApiLaravel\Objects;

interface ResponseSchemaWrapper
{
    public function wrap(OpenApiSchema $schema): OpenApiSchema;
}