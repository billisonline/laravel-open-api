<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiOperation extends DataTransferObject
{
   use DtoSerializationOptions;

    /** @var string */
    public $method;

    /** @var string */
    public $description = '';

    /** @var string */
    public $operationId = '';

    /** @var \BYanelli\OpenApiLaravel\OpenApiResponse[]|array  */
    public $responses = [];

    protected $exceptKeys = ['method'];

    public $ignoreKeysIfEmpty = ['description', 'operationId', 'responses'];

    public $keyArrayBy = [
        'responses' => 'status',
    ];
}
