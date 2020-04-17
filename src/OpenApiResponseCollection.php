<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class OpenApiResponseCollection extends DataTransferObjectCollection
{
    use DtoCollectionSerializationOptions;

    protected $stackBy = 'status';

    public function current(): OpenApiResponse
    {
        return parent::current();
    }
}