<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiPath extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $path;

    /** @var \BYanelli\OpenApiLaravel\OpenApiOperationCollection */
    public $operations;

    protected $rootKey = 'operations';
}