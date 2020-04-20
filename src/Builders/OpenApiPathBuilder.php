<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\OpenApiPath;
use Illuminate\Routing\Route;
use Illuminate\Support\Traits\Tappable;

class OpenApiPathBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $path;

    /**
     * @var OpenApiOperationBuilder[]|array
     */
    private $operations;

    /**
     * @param callable|array|string $action
     * @param callable|null $tapOperation
     * @return $this
     */
    public function fromAction($action, ?callable $tapOperation=null): self
    {
        if (is_array($action)) {$action = implode('@', $action);}

        $route = app('router')->getRouteByAction($action);

        return $this->fromRoute($route, $tapOperation);
    }

    public function fromRoute(Route $route, ?callable $tapOperation=null): self
    {
        $this->path($route->uri);

        $operation = (
            OpenApiOperationBuilder::make()
                ->method($this->getRouteNonHeadMethod($route))
                ->tap(function (OpenApiOperationBuilder $operation) use ($route) {
                    foreach ($route->parameterNames() as $parameterName) {
                        $operation->addParameter(
                            OpenApiParameterBuilder::make()
                                ->name($parameterName)
                                ->inPath()
                        );
                    }
                })
        );

        $this->addOperation($operation);

        if ($tapOperation) {$operation->tap($tapOperation);}

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function build(): OpenApiPath
    {
        return new OpenApiPath([
            'path' => $this->path,
            'operations' => collect($this->operations)->map->build()->all()
        ]);
    }

    private function getRouteNonHeadMethod(Route $route): string
    {
        return strtolower(
            collect($route->methods)
                ->filter(function (string $method) {return $method != 'HEAD';})
                ->first()
        );
    }

    public function addOperation(OpenApiOperationBuilder $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }
}