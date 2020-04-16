<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiOperation extends DataTransferObject
{
   use SerializationOptions;

    /** @var string */
    public $method;

    /** @var string */
    public $description = '';

    /** @var string */
    public $operationId = '';

    protected $exceptKeys = ['method'];

    public $ignoreKeysIfEmpty = ['description', 'operationId'];
}
