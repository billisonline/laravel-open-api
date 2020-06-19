<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiRequestBodyDto extends DataTransferObject
{
    use SerializationOptions;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto|null */
    public $jsonSchema;

    public $applyKeys = [
        'jsonSchema' => 'content.application/json.schema'
    ];
}