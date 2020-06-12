<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinitionBuilder
{
    use Tappable, StaticallyConstructible;

    const COMPONENT_TYPE_SCHEMA = 'schema';

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
    private $components;

    /**
     * @var ResponseSchemaWrapper
     */
    private $responseSchemaWrapper;
    
    /** 
     * @var array 
     */
    private $properties;

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

    public function getPropertiesInstance(string $class, string $scope): ?object
    {
        return $this->properties[$class][$scope] ?? null;
    }

    public function setPropertiesInstance(string $class, string $scope, object $instance): self
    {
        $this->properties[$class][$scope] = $instance;

        return $this;
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

    private function validateComponentType(string $type): void
    {
        if (!in_array($type, [self::COMPONENT_TYPE_SCHEMA])) {
            throw new \Exception;
        }
    }

    public function registerComponent(ComponentizableInterface $component)
    {
        $this->validateComponentType($component->getComponentType());

        [$type, $name, $object] = [
            Str::plural($component->getComponentType()),
            $component->getComponentName(),
            $component->getComponentObject(),
        ];

        $this->components[$type][$name] = $object;
    }

    public function refPath(ComponentizableInterface $component): string
    {
        $this->validateComponentType($component->getComponentType());

        [$type, $name] = [
            Str::plural($component->getComponentType()),
            $component->getComponentName(),
        ];

        return "#/components/{$type}/{$name}";
    }

    public function build()
    {
        $definitionParams = [
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
        ];

        // Components are registered while other objects are being built, so they must be added afterwards
        $definitionParams['components'] = $this->components;

        return new OpenApiDefinition($definitionParams);
    }
}