<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlockFactory;
use Spatie\Regex\Regex;

class Model
{
    /**
     * @var EloquentModel
     */
    private $model;

    public function __construct(EloquentModel $model)
    {
        $this->model = $model;
    }

    public function getColumnType(string $name)
    {
        return (
            $this->model
                ->getConnection()
                ->getSchemaBuilder()
                ->getColumnType($this->model->getTable(), $name)
        );
    }

    public function hasColumn(string $name)
    {
        return (
            $this->model
                ->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($this->model->getTable(), $name)
        );
    }

    public function hasGetMutator(string $name)
    {
        return $this->model->hasGetMutator($name);
    }

    public function getGetMutatorType(string $name)
    {
        $methodName = 'get'.Str::studly($name).'Attribute';

        $method = new \ReflectionMethod($this->model, $methodName);

        return $method->getReturnType()->getName();
    }

    public function model(): EloquentModel
    {
        return $this->model;
    }

    public function getClassDescription(): string
    {
        $class = new \ReflectionClass($this->model);

        $docBlockFactory = DocBlockFactory::createInstance();

        return $docBlockFactory->create($class->getDocComment() ?: '/**  */')->getSummary();
    }

    public function getPropertyDescription(string $name): string
    {
        $class = new \ReflectionClass($this->model);

        $docBlockFactory = DocBlockFactory::createInstance();

        $docBlock = $docBlockFactory->create($class->getDocComment() ?: '/**  */');

        return (
            collect($docBlock->getTags())
                ->whereInstanceOf(Property::class)
                ->filter(function (Property $property) use ($name): bool {
                    $pattern = '/\s*'.preg_quote($name).'\s*(.*)/';

                    return Regex::match($pattern, $property->getDescription()->getBodyTemplate())->hasMatch();
                })
                ->map(function (Property $property) use ($name): string {
                    $pattern = '/\s*'.preg_quote($name).'\s*(.*)/';

                    return Regex::match($pattern, $property->getDescription()->getBodyTemplate())->groupOr(1, '');
                })
                ->first() ?: ''
        );
    }
}