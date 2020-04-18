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

    public $ignoreKeysIfEmpty = [
        'description',
    ];

    protected function validateIn(string $in)
    {
        return in_array($in, ['path', 'query', 'header', 'cookie']);
    }
}