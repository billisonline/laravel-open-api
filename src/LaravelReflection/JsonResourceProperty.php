<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

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

    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $itemType;
    /**
     * @var string
     */
    private $itemResourceType;

    public function __construct(
        string $name,
        string $type,
        bool $isConditional,
        string $description = '',
        ?string $resourceType = null,
        string $itemType = '',
        string $itemResourceType = ''
    ) {
        $this->validateType($type);

        $this->name = $name;
        $this->type = $type;
        $this->isConditional = $isConditional;
        $this->resourceType = $resourceType;
        $this->description = $description;
        $this->itemType = $itemType;
        $this->itemResourceType = $itemResourceType;
    }

    private function validateType(string $type): void
    {
        if (!in_array($type, ['string', 'integer', 'boolean', 'json_resource', 'array'])) {
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

    public function itemResourceType(): ?string
    {
        return $this->itemResourceType;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isConditional(): bool
    {
        return $this->isConditional;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isJsonResource(): bool
    {
        return $this->type == 'json_resource';
    }

    public function isJsonResourceArray(): bool
    {
        return $this->type == 'array' && $this->itemType == 'json_resource' && !empty($this->itemResourceType);
    }
}