<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class OpenApiOperationCollection extends DataTransferObjectCollection
{
    use DtoCollectionSerializationOptions;

    protected $stackBy = 'method';

    public function current(): OpenApiOperationCollection
    {
        return parent::current();
    }
}