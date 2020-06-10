<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\Support\Action;
use BYanelli\OpenApiLaravel\Support\PathParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Tappable;

class OpenApiParameterBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $in;

    /**
     * @var string
     */
    private $description;

    /**
     * @var OpenApiSchemaBuilder
     */
    private $schema;

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function type(string $type): self
    {
        $this->schema = OpenApiSchemaBuilder::make()->type($type);

        return $this;
    }

    public function inPath()
    {
        $this->in = 'path';

        return $this;
    }

    public function inQuery()
    {
        $this->in = 'query';

        return $this;
    }

    public function build()
    {
        return new OpenApiParameter([
            'name'          => $this->name,
            'in'            => $this->in,
            'schema'        => optional($this->schema)->build(),
            'description'   => $this->description,
        ]);
    }

    public function fromPathParameter(PathParameter $parameter): self
    {
        $this->name($parameter->name());

        $this->inPath();

        if (!is_null($model = $parameter->boundModel())) {
            $this->description($this->getDescriptionFromModel($model, $parameter->action()));
        }

        return $this;
    }

    private function getDescriptionFromModel(Model $model, Action $action): string
    {
        $uppercaseKey = ucfirst($model->getKeyName());

        $modelName = class_basename($model);

        return "{$uppercaseKey} of the {$modelName} to {$action->actionMethod()}";
    }

    public function description(string $string)
    {
        $this->description = $string;

        return $this;
    }
}