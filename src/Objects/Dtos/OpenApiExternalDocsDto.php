<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiExternalDocsDto extends DataTransferObject
{
    /** @var string */
    public $description;

    /** @var string */
    public $url;
}