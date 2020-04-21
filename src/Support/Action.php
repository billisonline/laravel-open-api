<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Routing\Route as IlluminateRoute;

class Action
{
    /**
     * @var IlluminateRoute
     */
    private $route;

    public static function fromName($actionName): self
    {
        if (is_array($actionName)) {$actionName = implode('@', $actionName);}

        /** @var IlluminateRoute $route */
        $route = app('router')->getRouteByAction($actionName);

        return new static($route);
    }

    public function __construct(IlluminateRoute $route)
    {
        $this->route = $route;
    }

    public function uri(): string
    {
        return $this->route->uri;
    }

    public function httpMethod(): string
    {
        return strtolower(
            collect($this->route->methods)
                ->filter(function (string $method) {return $method != 'HEAD';})
                ->first()
        );
    }

    /**
     * @return PathParameter[]|\Iterator
     */
    public function pathParameters()
    {
        foreach ($this->route->parameterNames() as $parameterName) {
            yield new PathParameter($this, $parameterName);
        }
    }

    public function controller()
    {
        return $this->route->getController();
    }

    public function actionMethod(): string
    {
        return $this->route->getActionMethod();
    }
}