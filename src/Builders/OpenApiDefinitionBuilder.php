<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\OpenApiTag;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinitionBuilder
{
    use Tappable, StaticallyConstructible;

    const COMPONENT_TYPE_SCHEMA = 'schema';
    const COMPONENT_TYPE_RESPONSE = 'response';

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

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var bool
     */
    private $usingBearerTokenAuth = false;

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
        if (!in_array($type, [self::COMPONENT_TYPE_SCHEMA, self::COMPONENT_TYPE_RESPONSE])) {
            throw new \Exception;
        }
    }

    public function registerComponent(ComponentizableInterface $component)
    {
        $this->validateComponentType($component->getComponentType());

        [$type, $key, $object] = [
            Str::plural($component->getComponentType()),
            $component->getComponentKey(),
            $component->getComponentObject(),
        ];

        $this->components[$type][$key] = $object;
    }

    public function registerTag(string $name, string $description)
    {
        $this->tags[$name] = $description;
    }

    public function refPath(ComponentizableInterface $component): string
    {
        $this->validateComponentType($component->getComponentType());

        [$type, $key] = [
            Str::plural($component->getComponentType()),
            $component->getComponentKey(),
        ];

        return "#/components/{$type}/{$key}";
    }

    public function build()
    {
        $definitionParams = [
            'paths' => (
                collect($this->paths)
                    ->filter(function (OpenApiPathBuilder $path): bool {
                        return !is_null($path->getPath());
                    })
                    ->map(function (OpenApiPathBuilder $path): OpenApiPath {
                        return $path->build();
                    })
                    ->all()
            ),
            'info' => $this->info->build(),
            'usingBearerTokenAuth' => $this->usingBearerTokenAuth,
        ];

        // Components and tags are registered while other objects are being built, so they must be added afterwards
        $definitionParams['components'] = $this->components;

        $definitionParams['tags'] = (
            collect($this->tags)
                ->map(function (string $description, string $name): OpenApiTag {
                    return new OpenApiTag([
                        'name'          => $name,
                        'description'   => $description,
                    ]);
                })
                ->values()
                ->all()
        );

        return new OpenApiDefinition($definitionParams);
    }

    public function usingBearerTokenAuth(): self
    {
        $this->usingBearerTokenAuth = true;

        return $this;
    }
}