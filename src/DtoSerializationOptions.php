<?php

namespace BYanelli\OpenApiLaravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * @mixin DataTransferObject
 * @property array applyKeys
 * @property array ignoreKeysIfEmpty
 * @property array rootKey
 * @property array keyArrayBy
 */
trait DtoSerializationOptions
{
    public function toArray(): array
    {
        $result = [];

        $applyKeys = $this->applyKeys ?? [];
        $ignoreIfEmpty = $this->ignoreKeysIfEmpty ?? [];
        $keyArrayBy = $this->keyArrayBy ?? [];

        $meta = ['ignoreKeysIfEmpty', 'rootKey', 'keyArrayBy'];

        foreach (parent::toArray() as $key => $value) {
            $originalValue = $this->{$key} ?? null;

            if (in_array($key, $meta)) {
                continue;
            }

            if (empty($value) && in_array($key, $ignoreIfEmpty)) {
                continue;
            }

            if (
                is_array($originalValue)
                && in_array($key, array_keys($keyArrayBy))
            ) {
                $keyArrayByValue = $keyArrayBy[$key];

                $value = $this->keyArrayByDtoValue($keyArrayByValue, $originalValue);
            }

            $result[$key] = $value;

            if (in_array($key, array_keys($applyKeys))) {
                Arr::set($result, $applyKeys[$key], $value);

                unset($result[$key]);
            }

            if (method_exists($this, $customSerializer = 'serialize'.Str::studly($key))) {
                $customResult = $this->{$customSerializer}($originalValue);

                unset($result[$key]);

                $result = array_merge($result, $customResult);
            }
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