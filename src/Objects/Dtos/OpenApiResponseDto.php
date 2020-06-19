<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiResponseDto extends DataTransferObject
{
    use SerializationOptions;

    /** @var int */
    public $status;

    /** @var string */
    public $description = '';

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto|null */
    public $jsonSchema;

    protected $exceptKeys = ['status'];

    public $ignoreKeysIfEmpty = ['description', 'jsonSchema'];

    public $applyKeys = [
        'jsonSchema' => 'content.application/json.schema'
    ];
}