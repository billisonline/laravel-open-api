<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiRequestBodyDto;
use BYanelli\OpenApiLaravel\Objects\Tappable;

class OpenApiRequestBody
{
    use Tappable, StaticallyConstructible;

    /**
     * @var OpenApiSchema
     */
    private $jsonSchema;

    //todo: required
    //todo: form urlencoded schema

    public function jsonSchema(OpenApiSchema $jsonSchema): self
    {
        $this->jsonSchema = $jsonSchema;

        return $this;
    }

    public function build()
    {
        return new OpenApiRequestBodyDto([
            'jsonSchema' => $this->jsonSchema->build(),
        ]);
    }
}