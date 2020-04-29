<?php

namespace BYanelli\OpenApiLaravel\Support;

class JsonResourceProperty
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $isConditional;

    /**
     * @var string|null
     */
    private $resourceType;

    public function __construct(string $name, string $type, bool $isConditional, ?string $resourceType=null)
    {
        $this->validateType($type);

        $this->name = $name;
        $this->type = $type;
        $this->isConditional = $isConditional;
        $this->resourceType = $resourceType;
    }

    private function validateType(string $type): void
    {
        if (!in_array($type, ['string', 'integer', 'json_resource', 'json_resource_array'])) {
            throw new \Exception($type);
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function resourceType(): ?string
    {
        return $this->resourceType;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isConditional(): bool
    {
        return $this->isConditional;
    }
}