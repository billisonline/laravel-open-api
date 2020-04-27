<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Schema;

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

    public function getAttributeType(string $name)
    {
        // if has get$nameAttribute method, reflect return type

        // if has $name method and it returns a relation:
        // - reflect model -> resource type
        // - determine whether resource is singular or plural

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
}