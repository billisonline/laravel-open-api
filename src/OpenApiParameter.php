<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiParameter extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $name;

    /** @var string */
    public $in;

    /** @var string */
    public $description = '';

    /** @var bool */
    public $required = false;

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema|null */
    public $schema;

    public $ignoreKeysIfEmpty = [
        'description',
        'schema',
    ];

    protected function validateIn(string $in)
    {
        return in_array($in, ['path', 'query', 'header', 'cookie']);
    }
}