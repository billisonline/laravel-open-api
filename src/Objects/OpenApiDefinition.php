<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiDefinitionDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiTagDto;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinition
{
    use Tappable, StaticallyConstructible;

    const COMPONENT_TYPE_SCHEMA = 'schema';
    const COMPONENT_TYPE_RESPONSE = 'response';

    /**
     * @var self|null
     */
    protected static $current = null;

    /**
     * @var OpenApiPath[]|array
     */
    private $paths = [];

    /**
     * @var OpenApiInfo
     */
    private $info;

    /**
     * @var OpenApiSchema[]|array
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

    public function wrapResponseSchema(OpenApiSchema $schema): OpenApiSchema
    {
        if (!is_null($this->responseSchemaWrapper)) {
            return $this->responseSchemaWrapper->wrap($schema);
        }

        return $schema;
    }

    public function addPath(OpenApiPath $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function findOrCreatePath(string $path): OpenApiPath
    {
        if ($existing = $this->findPath($path)) {
            return $existing;
        }

        return new OpenApiPath($path);
    }

    public function findPath(string $pathToFind): ?OpenApiPath
    {
        return (
            collect($this->paths)
                ->filter(function (OpenApiPath $path) use ($pathToFind) {
                    return $path->getPath() == $pathToFind;
                })
                ->first()
        );
    }

    public function info(OpenApiInfo $info)
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
                    ->filter(function (OpenApiPath $path): bool {
                        return !is_null($path->getPath());
                    })
                    ->map(function (OpenApiPath $path): OpenApiPathDto {
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
                ->map(function (string $description, string $name): OpenApiTagDto {
                    return new OpenApiTagDto([
                        'name'          => $name,
                        'description'   => $description,
                    ]);
                })
                ->values()
                ->all()
        );

        return new OpenApiDefinitionDto($definitionParams);
    }

    public function usingBearerTokenAuth(): self
    {
        $this->usingBearerTokenAuth = true;

        return $this;
    }
}