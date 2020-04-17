<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * @mixin DataTransferObject
 * @property array ignoreKeysIfEmpty
 * @property array rootKey
 * @property array keyArrayBy
 */
trait DtoSerializationOptions
{
    public function toArray(): array
    {
        $result = [];

        $ignoreIfEmpty = $this->ignoreKeysIfEmpty ?? [];

        $keyArrayBy = $this->keyArrayBy ?? [];

        $meta = ['ignoreKeysIfEmpty', 'rootKey', 'keyArrayBy'];

        foreach (parent::toArray() as $key => $value) {
            if (in_array($key, $meta)) {
                continue;
            }

            if (empty($value) && in_array($key, $ignoreIfEmpty)) {
                continue;
            }

            if (
                // Get original value (not serialized from parent::toArray())
                is_array($arr = ($this->{$key} ?? null))
                && in_array($key, array_keys($keyArrayBy))
            ) {
                $keyArrayByValue = $keyArrayBy[$key];

                $value = $this->keyArrayByDtoValue($keyArrayByValue, $arr);
            }

            $result[$key] = $value;
        }

        if (!empty($rootKey = $this->rootKey ?? null)) {
            $result = $result[$rootKey];
        }

        return $result;
    }

    protected function keyArrayByDtoValue(string $keyName, array $arr): array
    {
        /** @var DataTransferObject $dto */
        foreach ($arr as $i => $dto) {
            $arr[$dto->{$keyName}] = $dto->toArray();

            unset($arr[$i]);
        }

        return $arr;
    }
}