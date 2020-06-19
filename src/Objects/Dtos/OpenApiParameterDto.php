<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiParameterDto extends DataTransferObject
{
    use SerializationOptions;

    /** @var string */
    public $name;

    /** @var string */
    public $in;

    /** @var string */
    public $description = '';

    /** @var bool */
    public $required = false;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto|null */
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