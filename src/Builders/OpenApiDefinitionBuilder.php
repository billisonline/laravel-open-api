<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinitionBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var self|null
     */
    protected static $current = null;

    /**
     * @var OpenApiPathBuilder[]|array
     */
    private $paths = [];

    /**
     * @var OpenApiInfoBuilder
     */
    private $info;

    /**
     * @var OpenApiSchemaBuilder[]|array
     */
    private $resourceSchemas;

    /**
     * @var OpenApiSchemaBuilder[]|array
     */
    private $resourceModels;

    /**
     * @var ResponseSchemaWrapper
     */
    private $responseSchemaWrapper;

    public static function with(callable $callback): self
    {
        $current = static::$current = new static;

        $callback();

        static::$current = null;

        return $current;
    }

    public static function getCurrent(): ?self
    {
        return static::$current;
    }

    public function responseSchemaWrapper(ResponseSchemaWrapper $wrapper)
    {
        $this->responseSchemaWrapper = $wrapper;
        
        return $this;
    }

    public function wrapResponseSchema(OpenApiSchemaBuilder $schema): OpenApiSchemaBuilder
    {
        if (!is_null($this->responseSchemaWrapper)) {
            return $this->responseSchemaWrapper->wrap($schema);
        }

        return $schema;
    }

    public function addPath(OpenApiPathBuilder $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function findOrCreatePath(string $path): OpenApiPathBuilder
    {
        if ($existing = $this->findPath($path)) {
            return $existing;
        }

        return new OpenApiPathBuilder($path);
    }

    public function registerResourceModel(string $resource, string $model): self
    {
        $this->resourceModels[$resource] = $model;

        return $this;
    }

    public function getRegisteredResourceModel(string $resource): ?string
    {
        return $this->resourceModels[$resource] ?? null;
    }

    public function registerResourceSchema(JsonResource $resource, OpenApiSchemaBuilder $schema): void
    {
        $resourceClass = $resource->resourceClass();

        $schema->name(class_basename($resourceClass));

        if (!isset($this->resourceSchemas[$resourceClass])) {
            $this->resourceSchemas[$resourceClass] = $schema;
        }
    }

    private function getRefPathForResource(JsonResource $resource): string
    {
        $name = class_basename($resource->resourceClass());

        return "#/components/schemas/resources/{$name}";
    }

    public function getSchemaRefForResource(JsonResource $resource): OpenApiSchemaBuilder
    {
        return OpenApiSchemaBuilder::make()->ref($this->getRefPathForResource($resource));
    }

    public function findPath(string $pathToFind): ?OpenApiPathBuilder
    {
        return (
            collect($this->paths)
                ->filter(function (OpenApiPathBuilder $path) use ($pathToFind) {
                    return $path->getPath() == $pathToFind;
                })
                ->first()
        );
    }

    public function info(OpenApiInfoBuilder $info)
    {
        $this->info = $info;

        return $this;
    }

    public function build()
    {
        return new OpenApiDefinition([
            'resourceSchemas' => collect($this->resourceSchemas)->map->build()->all(),
            'paths' => (
                collect($this->paths)
                    ->filter(function (OpenApiPathBuilder $path) {
                        return !is_null($path->getPath());
                    })
                    ->map(function (OpenApiPathBuilder $path) {
                        return $path->build();
                    })
                    ->all()
            ),
            'info' => $this->info->build()
        ]);
    }
}