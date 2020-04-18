<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiExternalDocs extends DataTransferObject
{
    /** @var string */
    public $description;

    /** @var string */
    public $url;
}