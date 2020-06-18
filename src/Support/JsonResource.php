<?php

namespace BYanelli\OpenApiLaravel\Support;

use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
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
     * @var JsonResourceProperties
     */
    protected $definedProperties;

    public function __construct(string $resourceClass)
    {
        if (!is_subclass_of($resourceClass, BaseJsonResource::class)) {
            throw new \Exception;
        }

        $this->resourceClass = $resourceClass;
        $this->definedProperties = JsonResourceProperties::for($resourceClass);
    }

    protected function model(): Model
    {
        if ($model = $this->definedProperties->modelInstance()) {
            return $model;
        }

        $resourceClass = $this->resourceClass;
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

    protected function spyOnProperties()
    {
        $className = $this->resourceClass;

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
            collect($this->spyOnProperties())
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
        $model = $this->model();

        $accessor = $propertySpy->accessor();
        $isConditional = $propertySpy->isConditional();

        if ($model->hasColumn($accessor)) {
            $type = $this->fixDatabaseType($model->getColumnType($accessor));
        }

        if ($model->hasGetMutator($accessor)) {
            $type = $this->fixPhpType($model->getGetMutatorType($accessor));
        }

        if (!isset($type)) {
            throw new \Exception($accessor);
        }

        $description = $model->getDescription($accessor);

        return new JsonResourceProperty($name, $type, $isConditional, $description);
    }

    private function convertJsonResourceToProperty(BaseJsonResource $resource, string $name): JsonResourceProperty
    {
        return new JsonResourceProperty($name, 'json_resource', false, '', get_class($resource));
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

    /**
     * @return string
     */
    public function modelClass(): string
    {
        return get_class($this->model()->model());
    }

    protected function fixPhpType(string $type): string
    {
        if ($type == 'bool') {
            return 'boolean';
        }

        return $type;
    }

    public function schema(): OpenApiSchemaBuilder
    {
        if ($schema = $this->definedProperties->schema()) {
            return $schema;
        }

        return OpenApiSchemaBuilder::make()->tap(function (OpenApiSchemaBuilder $schema) {
            $schema->type('object');

            foreach ($this->properties() as $property) {
                if ($property->type() == 'json_resource') {continue;} //todo: refs

                $schema->addProperty(
                    OpenApiSchemaBuilder::make()
                        ->name($property->name())
                        ->type($property->type())
                        ->nullable($property->isConditional())
                );
            }
        });
    }

    public function definedProperties(): JsonResourceProperties
    {
        return $this->definedProperties;
    }

    public function componentKey(): string
    {
        return class_basename($this->resourceClass);
    }

    public function componentTitle(): string
    {
        return preg_replace('/Resource$/', '', class_basename($this->resourceClass));
    }
}