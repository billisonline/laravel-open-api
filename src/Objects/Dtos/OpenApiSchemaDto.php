<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiSchemaDto extends DataTransferObject
{
    use SerializationOptions {
        toArray as _toArray;
    }

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

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto|null */
    public $items;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto[]|array */
    public $properties = [];

    /** @var string|null */
    public $componentKey;

    /** @var string|null */
    public $componentTitle;

    /** @var string|null */
    public $description;

    public $keyArrayBy = [
        'properties' => 'name',
    ];

    protected $exceptKeys = [
        'componentKey',
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
        'properties',
        'componentTitle',
        'description',
    ];

    public $applyKeys = [
        'componentTitle' => 'title',
    ];

    protected function validateType(string $type)
    {
        return in_array($type, ['string', 'number', 'integer', 'boolean', 'array', 'object']);
    }
}