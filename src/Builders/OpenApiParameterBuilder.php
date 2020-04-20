<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
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

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function inPath()
    {
        $this->in = 'path';

        return $this;
    }

    public function build()
    {
        return new OpenApiParameter([
            'name' => $this->name,
            'in' => $this->in,
            'description' => $this->description,
        ]);
    }

    public function fromRouteParameter(Route $route, string $parameterName): self
    {
        $this->name($parameterName)->inPath();

        if (!is_null($model = $this->getBoundModel($route, $parameterName))) {
            $this->zzz($model, $route);
        }

        return $this;
    }

    private function getBoundModel(Route $route, string $parameterName): ?Model
    {
        $controller = $route->getController();

        $method = new \ReflectionMethod($controller, $route->getActionMethod());

        /** @var \ReflectionParameter|null $boundModelParameter */
        $boundModelParameter = (
            collect($method->getParameters())
                ->filter(function (\ReflectionParameter $param) use ($parameterName): bool {
                    return (
                        ($param->getName() == $parameterName)
                        && $param->hasType()
                        && ($param->getType() instanceof \ReflectionNamedType)
                        && is_subclass_of($param->getType()->getName(), Model::class)
                    );
                })
                ->first()
        );

        if (is_null($boundModelParameter)) {
            return null;
        }

        $modelClass = $boundModelParameter->getType()->getName();

        return new $modelClass;
    }

    private function zzz(Model $model, Route $route)
    {
        $uppercaseKey = ucfirst($model->getKeyName());

        $modelName = class_basename($model);

        $this->description("{$uppercaseKey} of the {$modelName} to {$route->getActionMethod()}");
    }

    public function description(string $string)
    {
        $this->description = $string;

        return $this;
    }
}