<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiDefinition extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $openapi = '3.0.0';

    /** @var \BYanelli\OpenApiLaravel\OpenApiPath[]|array */
    public $paths = [];

    public $keyArrayBy  = [
        'paths' => 'path',
    ];
}