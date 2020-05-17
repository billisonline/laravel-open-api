<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Spatie\Regex\Regex;

class JsonResource
{
    /**
     * @var string
     */
    private $resourceClass;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var array
     */
    protected $spiedProperties;

    public function __construct(string $resourceClass, ?Model $model=null)
    {
        if (!is_subclass_of($resourceClass, BaseJsonResource::class)) {
            throw new \Exception;
        }

        $this->resourceClass = $resourceClass;
        $this->model = $model ?: $this->guessModel($resourceClass);
        $this->spiedProperties = $this->spyOnProperties($resourceClass);
    }

    protected function guessModel(string $resourceClass): Model
    {
        $baseNamespace = Regex::match('/^\\\\?([\w\d]+)\\\\/', $resourceClass)->group(1);

        $className = class_basename($resourceClass);

        if (class_exists($modelClass = "\\{$baseNamespace}\\Models\\{$className}")) {
            return new Model(new $modelClass);
        }

        if (class_exists($modelClass = "\\{$baseNamespace}\\{$className}")) {
            return new Model(new $modelClass);
        }

        throw new \Exception;
    }

    protected function spyOnProperties(string $className)
    {
        $spy = new JsonResourceSpy();

        /** @var BaseJsonResource $instance */
        /** @noinspection ALL */
        $instance = eval('return new class ($spy) extends '.$className.' { use \BYanelli\OpenApiLaravel\Support\AlwaysEvaluatesConditionalProperties; };');

        return $instance->toArray(Request::createFromGlobals());
    }

    /**
     * @return JsonResourceProperty[]|array
     */
    public function properties(): array
    {
        return (
            collect($this->spiedProperties)
                ->map(function ($rawProperty, string $name): JsonResourceProperty {
                    return $this->convertRawProperty($rawProperty, $name);
                })
                ->values()
                ->all()
        );
    }

    private function convertRawProperty($rawProperty, string $name): JsonResourceProperty
    {
        // if resource collection

        if ($rawProperty instanceof BaseJsonResource) {
            return $this->convertJsonResourceToProperty($rawProperty, $name);
        }

        if ($rawProperty instanceof JsonResourcePropertySpy) {
            return $this->convertSpyToProperty($rawProperty, $name);
        }

        throw new \Exception;
    }

    private function convertSpyToProperty(JsonResourcePropertySpy $propertySpy, string $name): JsonResourceProperty
    {
        $accessor = $propertySpy->accessor();
        $isConditional = $propertySpy->isConditional();

        if ($this->model->hasColumn($accessor)) {
            $type = $this->fixDatabaseType($this->model->getColumnType($accessor));
        }

        if ($this->model->hasGetMutator($accessor)) {
            $type = $this->fixPhpType($this->model->getGetMutatorType($accessor));
        }

        if (!isset($type)) {
            throw new \Exception($accessor);
        }

        return new JsonResourceProperty($name, $type, $isConditional);
    }

    private function convertJsonResourceToProperty(BaseJsonResource $resource, string $name): JsonResourceProperty
    {
        return new JsonResourceProperty($name, 'json_resource', false, get_class($resource));
    }

    private function fixDatabaseType(string $type): string
    {
        if ($type == 'text') {
            return 'string';
        }

        if ($type == 'bigint') {
            return 'integer';
        }

        if ($type == 'date') {
            return 'string';
        }

        if ($type == 'bool') {
            return 'boolean';
        }

        return $type;
    }

    /**
     * @return string
     */
    public function resourceClass(): string
    {
        return $this->resourceClass;
    }

    protected function fixPhpType(string $type): string
    {
        if ($type == 'bool') {
            return 'boolean';
        }

        return $type;
    }
}