<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiTagDto extends DataTransferObject
{
    use SerializationOptions;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiExternalDocsDto|null */
    public $externalDocs;

    public $ignoreKeysIfEmpty = [
        'externalDocs'
    ];
}