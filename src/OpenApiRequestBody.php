<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiRequestBody extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema|null */
    public $jsonSchema;

    public $applyKeys = [
        'jsonSchema' => 'content.application/json.schema'
    ];
}