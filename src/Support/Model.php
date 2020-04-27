<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

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
}