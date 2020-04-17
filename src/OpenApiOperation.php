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

    /** @var \BYanelli\OpenApiLaravel\OpenApiResponseCollection|null  */
    public $responses = null;

    protected $exceptKeys = ['method'];

    public $ignoreKeysIfEmpty = ['description', 'operationId', 'responses'];
}
