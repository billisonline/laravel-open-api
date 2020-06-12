<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiSchema extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $type;

    /** @var int|float|null */
    public $minimum;

    /** @var int|float|null */
    public $maximum;

    /** @var int|float|null */
    public $multipleOf;

    /** @var int|null */
    public $minLength;

    /** @var int|null */
    public $maxLength;

    /** @var string|null */
    public $pattern;

    /** @var bool|null */
    public $nullable;

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema|null */
    public $items;

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema[]|array */
    public $properties = [];

    /** @var string|null */
    public $componentName;

    public $keyArrayBy = [
        'properties' => 'name',
    ];

    protected $exceptKeys = [
        'componentName',
    ];

    public $ignoreKeysIfEmpty = [
        'minimum',
        'maximum',
        'multipleOf',
        'minLength',
        'maxLength',
        'pattern',
        'nullable',
        'items',
        'properties'
    ];

    protected function validateType(string $type)
    {
        return in_array($type, ['string', 'number', 'integer', 'boolean', 'array', 'object']);
    }
}