<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiPathDto extends DataTransferObject
{
    use SerializationOptions;

    /** @var string */
    public $path;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiOperationDto[]|array */
    public $operations = [];

    protected $rootKey = 'operations';

    public $keyArrayBy = [
        'operations' => 'method',
    ];
}