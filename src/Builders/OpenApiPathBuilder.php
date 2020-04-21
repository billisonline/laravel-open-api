<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Support\Action;
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
    public function fromActionName($action, ?callable $tapOperation=null): self
    {
        $action = Action::fromName($action);

        return (new static)->fromAction($action, $tapOperation);
    }

    public function fromAction(Action $action, ?callable $tapOperation=null): self
    {
        $this->path($action->uri());

        $operation = (
            OpenApiOperationBuilder::make()
                ->method($action->httpMethod())
                ->tap(function (OpenApiOperationBuilder $operation) use ($action) {
                    foreach ($action->pathParameters() as $parameter) {
                        $operation->addParameter(
                            OpenApiParameterBuilder::make()->fromPathParameter($parameter)
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

    public function addOperation(OpenApiOperationBuilder $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }
}