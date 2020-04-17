<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * @mixin DataTransferObject
 * @property array ignoreKeysIfEmpty
 * @property array rootKey
 */
trait DtoSerializationOptions
{
    public function toArray(): array
    {
        $result = [];

        $ignoreIfEmpty = $this->ignoreKeysIfEmpty ?? [];

        $meta = ['ignoreKeysIfEmpty', 'rootKey'];

        foreach (parent::toArray() as $key => $value) {
            if (in_array($key, $meta)) {
                continue;
            }

            if (empty($value) && in_array($key, $ignoreIfEmpty)) {
                continue;
            }

            $result[$key] = $value;
        }

        if (!empty($rootKey = $this->rootKey ?? null)) {
            $result = $result[$rootKey];
        }

        return $result;
    }
}