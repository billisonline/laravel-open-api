<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiPath extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $path;

    /** @var \BYanelli\OpenApiLaravel\OpenApiOperation[]|array */
    public $operations = [];

    protected $rootKey = 'operations';

    public $keyArrayBy = [
        'operations' => 'method',
    ];
}