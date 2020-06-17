<?php

namespace BYanelli\OpenApiLaravel\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlockFactory;
use Spatie\Regex\Regex;

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

    public function objectName()
    {
        $controllerName = class_basename($this->controller());

        return Regex::match('/^([A-Za-z]+)Controller$/', $controllerName)->groupOr(1, $controllerName);
    }

    public function operationId(): string
    {
        return Str::camel("{$this->objectName()} {$this->actionMethod()}");
    }

    public function responseClass(): ?string
    {
        $method = new \ReflectionMethod($this->controller(), $this->actionMethod());

        if (is_null($returnType = $method->getReturnType())) {
            return null;
        }

        if (!($returnType instanceof \ReflectionNamedType)) {
            return null;
        }

        return $returnType->getName();
    }

    public function isPlural(): bool
    {
        return $this->actionMethod() == 'index'; //todo
    }

    public function formRequestClass(): ?string
    {
        $method = new \ReflectionMethod($this->controller(), $this->actionMethod());

        foreach ($method->getParameters() as $parameter) {
            if (is_null($type = $parameter->getType())) {
                continue;
            }

            if (!($type instanceof \ReflectionNamedType)) {
                continue;
            }

            $className = $type->getName();

            if (is_subclass_of($className, FormRequest::class)) {
                return $className;
            }
        }

        return null;
    }

    public function requestComponentKey(): string
    {
        return $this->requestComponentTitle();
    }

    public function requestComponentTitle(): string
    {
        return Str::studly($this->operationId().'Request');
    }

    public function responseComponentKey(): string
    {
        return $this->responseComponentTitle();
    }

    public function responseComponentTitle(): string
    {
        return Str::studly($this->operationId().'Response');
    }

    public function tagName(): string
    {
        return $this->objectName();
    }

    public function tagDescription(): string
    {
        $docBlockFactory = DocBlockFactory::createInstance();

        $class = new \ReflectionClass($this->controller());

        return $docBlockFactory->create($class->getDocComment() ?: '/** */')->getSummary();
    }

    public function description(): string
    {
        $docBlockFactory = DocBlockFactory::createInstance();

        $method = new \ReflectionMethod($this->controller(), $this->actionMethod());

        return $docBlockFactory->create($method->getDocComment() ?: '/** */')->getSummary();
    }
}