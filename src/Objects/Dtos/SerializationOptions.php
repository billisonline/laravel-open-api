<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * @mixin DataTransferObject
 * @property array applyKeys
 * @property array ignoreKeysIfEmpty
 * @property array ignoreKeysIfNull
 * @property array rootKey
 * @property array keyArrayBy
 */
trait SerializationOptions
{
    public function toArray(): array
    {
        $result = [];

        $applyKeys = $this->applyKeys ?? [];
        $ignoreIfEmpty = $this->ignoreKeysIfEmpty ?? [];
        $ignoreIfNull = $this->ignoreKeysIfNull ?? [];
        $keyArrayBy = $this->keyArrayBy ?? [];

        $meta = ['applyKeys', 'ignoreKeysIfEmpty', 'rootKey', 'keyArrayBy'];

        foreach (parent::toArray() as $key => $value) {
            $originalValue = $this->{$key} ?? null;

            if (in_array($key, $meta)) {
                continue;
            }

            $customValidator = $this->getCustomValidatorName($key);

            if (method_exists($this, $customValidator)) {
                $validationResult = $this->{$customValidator}($originalValue);

                if (!$validationResult) {
                    throw new \Exception("Invalid {$key}");
                }
            }

            if (empty($value) && in_array($key, $ignoreIfEmpty)) {
                continue;
            }

            if (is_null($value) && in_array($key, $ignoreIfNull)) {
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

            $customSerializer = $this->getCustomSerializerName($key);

            if (method_exists($this, $customSerializer)) {
                $serializationResult = $this->{$customSerializer}($originalValue);

                unset($result[$key]);

                $result = array_merge($result, $serializationResult);
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

    protected function getCustomSerializerName(string $key): string
    {
        return 'serialize'.Str::studly($key);
    }

    protected function getCustomValidatorName(string $key): string
    {
        return 'validate'.Str::studly($key);
    }
}