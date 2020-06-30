<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiDefinitionDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiTagDto;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

/**
 * Fluently build an OpenAPI definition. The {@link OpenApiDefinition::with} method collects all info, paths, etc.
 * for the definition.
 *
 * @see https://swagger.io/docs/specification/basic-structure/
 */
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

    /**
     * @var OpenApiOperation[]|array
     */
    private $operationsWithImplicitPaths;

    /**
     * Create a new definition object and set it as the current definition while the given callable is run. Paths,
     * info, operations, etc. constructed inside the callable will use this object as their current definition.
     *
     * @param callable $callback
     * @return static
     */
    public static function with(callable $callback): self
    {
        $current = static::$current = new static;

        $callback();

        static::$current = null;

        return $current;
    }

    /**
     * Get the current definition object if we are in a definition context (cf. {@link with}); otherwise return null.
     *
     * @return static|null
     */
    public static function current(): ?self
    {
        return static::$current;
    }

    /**
     * Get an instance of the user-defined "properties" object for the given type (e.g., JsonResource, FormRequest) and
     * subclass of that type (e.g., UserResource, CreatePostRequest)
     *
     * @param string $propertiesType
     * @param string $subclass
     * @return object|null
     */
    public function getPropertiesInstance(string $propertiesType, string $subclass): ?object
    {
        return $this->properties[$propertiesType][$subclass] ?? null;
    }

    /**
     * Set an instance of the user-defined "properties" object for the given type (e.g., JsonResource, FormRequest) and
     * subclass of that type (e.g., UserResource, CreatePostRequest). This instance will be subsequently returned by
     * {@link getPropertiesInstance}
     *
     * @param string $propertiesType
     * @param string $subclass
     * @param object $instance
     * @return OpenApiDefinition
     */
    public function setPropertiesInstance(string $propertiesType, string $subclass, object $instance): self
    {
        $this->properties[$propertiesType][$subclass] = $instance;

        return $this;
    }

    /**
     * Set a "wrapper" which will wrap all response schemas in an outer object. This may be useful if, for example, your
     * endpoints return JSON data in a `data` or `content` key.
     *
     * @param ResponseSchemaWrapper $wrapper
     * @return $this
     */
    public function responseSchemaWrapper(ResponseSchemaWrapper $wrapper)
    {
        $this->responseSchemaWrapper = $wrapper;
        
        return $this;
    }

    /**
     * If a response schema wrapper has been set, apply it to the given schema.
     *
     * @param OpenApiSchema $schema
     * @return OpenApiSchema
     */
    public function wrapResponseSchema(OpenApiSchema $schema): OpenApiSchema
    {
        if (!is_null($this->responseSchemaWrapper)) {
            return $this->responseSchemaWrapper->wrap($schema);
        }

        return $schema;
    }

    /**
     * Add a path to the definition.
     *
     * @param OpenApiPath $path
     * @return $this
     */
    public function addPath(OpenApiPath $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Set the "info" block for this definition.
     *
     * @param OpenApiInfo $info
     * @return $this
     */
    public function info(OpenApiInfo $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Ensure the given component type is valid or throw an exception. Note: not all component types that are valid in
     * the OpenAPI 3.0 spec are supported yet.
     *
     * @see https://swagger.io/docs/specification/components/
     *
     * @param string $type
     * @throws \Exception
     */
    private function validateComponentType(string $type): void
    {
        if (!in_array($type, [self::COMPONENT_TYPE_SCHEMA, self::COMPONENT_TYPE_RESPONSE])) {
            throw new \Exception;
        }
    }

    /**
     * Register a component in the components section.
     *
     * @param ComponentizableInterface $component
     * @throws \Exception
     */
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

    /**
     * Register a tag in the tags section.
     *
     * @see https://swagger.io/docs/specification/grouping-operations-with-tags/
     *
     * @param string $name
     * @param string $description
     */
    public function registerTag(string $name, string $description)
    {
        $this->tags[$name] = $description;
    }

    /**
     * Get the `$ref` path to reference a component from elsewhere in the definition.
     *
     * @see https://swagger.io/docs/specification/using-ref/
     *
     * @param ComponentizableInterface $component
     * @return string
     * @throws \Exception
     */
    public function refPath(ComponentizableInterface $component): string
    {
        $this->validateComponentType($component->getComponentType());

        [$type, $key] = [
            Str::plural($component->getComponentType()),
            $component->getComponentKey(),
        ];

        return "#/components/{$type}/{$key}";
    }

    public function build(): OpenApiDefinitionDto
    {
        $this->paths = array_merge($this->paths, $this->createPathsFromOperationsWithImplicitPaths());

        $definitionParams = [
            'paths'                 => $this->collectPaths(),
            'info'                  => $this->info->build(),
            'usingBearerTokenAuth'  => $this->usingBearerTokenAuth,
        ];

        // Components and tags are registered while other objects are being built, so they must be added afterwards
        $definitionParams['components'] = $this->components;

        $definitionParams['tags'] = $this->collectTags();

        return new OpenApiDefinitionDto($definitionParams);
    }

    /**
     * Collect and build all registered tags.
     *
     * @return OpenApiTagDto[]|array
     */
    protected function collectTags(): array
    {
        return (
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
    }

    /**
     * Specify that this definition has one or more paths authenticating with an `Authorization: Bearer {token}` header.
     *
     * @return $this
     */
    public function usingBearerTokenAuth(): self
    {
        $this->usingBearerTokenAuth = true;

        return $this;
    }

    /**
     * Collect and build all registered paths.
     *
     * @return OpenApiPathDto[]|array
     */
    protected function collectPaths(): array
    {
        return (
            collect($this->paths)
                ->map(function (OpenApiPath $path): OpenApiPathDto {
                    return $path->build();
                })
                ->all()
        );
    }

    public function addOperationWithImplicitPath(OpenApiOperation $operation)
    {
        if (! $operation->hasImplicitPath()) {
            throw new \Exception;
        }

        $this->operationsWithImplicitPaths[] = $operation;
    }

    private function createPathsFromOperationsWithImplicitPaths(): array
    {
        $findPathByUri = function (array $paths, string $uri): ?OpenApiPath {
            return (
                collect($paths)
                    ->filter(function (OpenApiPath $path) use ($uri) {
                        return $path->getPath() == $uri;
                    })
                    ->first()
            );
        };

        return collect($this->operationsWithImplicitPaths)->reduce(function (array $paths, OpenApiOperation $nextOperation) use ($findPathByUri) {
            if (is_null($action = $nextOperation->getImplicitPathAction())) {
                throw new \Exception; // todo: implicit paths not from actions?
            }

            if ($path = $findPathByUri($paths, $action->uri())) {
                $path->addOperation($nextOperation);
            } else {
                $path = OpenApiPath::fromAction($action)->addOperation($nextOperation);

                $paths[] = $path;
            }

            return $paths;
        }, []);
    }
}