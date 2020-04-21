<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Support\Action;
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

    public function __construct()
    {
        if ($currentDef = OpenApiDefinitionBuilder::getCurrent()) {
            $currentDef->addPath($this);
        }
    }

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
        return (
            $this->path($action->uri())
                ->addOperation(
                    OpenApiOperationBuilder::make()
                        ->fromAction($action)
                        ->tap($tapOperation ?: function () {})
                )
        );
    }

    public function path(string $path): self
    {
        if ($currentDef = OpenApiDefinitionBuilder::getCurrent()) {
            if ($existing = $currentDef->findPath($path)) {
                $currentDef->forgetPath($this); //todo this isn't great

                return $existing;
            }
        }

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

    public function getPath()
    {
        return $this->path; //todo all setters should be "withX"
    }
}