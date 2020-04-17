<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

trait DtoCollectionSerializationOptions
{
    public function toArray(): array
    {
        $stackBy = $this->stackBy;

        $collection = $this->collection;

        foreach ($collection as $key => $item) {
            if (
                ! $item instanceof DataTransferObject
                && ! $item instanceof DataTransferObjectCollection
            ) {
                continue;
            }

            $collection[$item->{$stackBy}] = $item->toArray();

            unset($collection[$key]);
        }

        return $collection;
    }
}