<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use Illuminate\Database\Eloquent\Model;

class PathParameter
{
    /**
     * @var Action
     */
    private $action;
    /**
     * @var string
     */
    private $name;

    public function __construct(Action $action, string $name)
    {
        $this->action = $action;
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function action()
    {
        return $this->action;
    }

    public function boundModel(): ?Model
    {
        $controller = $this->action->controller();

        $method = new \ReflectionMethod($controller, $this->action->actionMethod());

        /** @var \ReflectionParameter|null $boundModelParameter */
        $boundModelParameter = (
            collect($method->getParameters())
                ->filter(function (\ReflectionParameter $param): bool {
                    return (
                        ($param->getName() == $this->name)
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
}