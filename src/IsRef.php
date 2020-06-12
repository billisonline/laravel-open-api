<?php

namespace BYanelli\OpenApiLaravel;

/**
 * @property array additionalParams
 */
trait IsRef
{
    /** @var string */
    public $ref;

    public function __construct(array $parameters = [])
    {
        $this->ref = $parameters['ref'] ?? null;

        $additionalParams = $this->additionalParams ?? [];

        foreach ($additionalParams as $name) {
            $this->{$name} = $parameters[$name] ?? null;
        }
    }

    protected function getFieldValidators(): array
    {
        return [];
    }

    public function toArray(): array
    {
        return [
            '$ref' => $this->ref,
        ];
    }
}