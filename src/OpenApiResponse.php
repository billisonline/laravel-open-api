<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiResponse extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var int */
    public $status;

    /** @var string */
    public $description = '';

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema|null */
    public $jsonSchema;

    protected $exceptKeys = ['status'];

    public $ignoreKeysIfEmpty = ['description', 'jsonSchema'];

    public $applyKeys = [
        'jsonSchema' => 'content.application/json.schema'
    ];
}