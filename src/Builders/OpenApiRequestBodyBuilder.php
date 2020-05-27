<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiRequestBody;
use BYanelli\OpenApiLaravel\Support\Tappable;

class OpenApiRequestBodyBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $jsonSchema;

    //todo: required
    //todo: form urlencoded schema

    public function jsonSchema(OpenApiSchemaBuilder $jsonSchema): self
    {
        $this->jsonSchema = $jsonSchema;

        return $this;
    }

    public function build()
    {
        return new OpenApiRequestBody([
            'jsonSchema' => $this->jsonSchema->build(),
        ]);
    }
}