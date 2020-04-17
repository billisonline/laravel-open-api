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

    protected $exceptKeys = ['status'];

    public $ignoreKeysIfEmpty = ['description'];
}