<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiTag extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var \BYanelli\OpenApiLaravel\OpenApiExternalDocs|null */
    public $externalDocs;

    public $ignoreKeysIfEmpty = [
        'externalDocs'
    ];
}