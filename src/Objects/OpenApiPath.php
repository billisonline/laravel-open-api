<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\LaravelReflection\Action;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin OpenApiOperation
 */
class OpenApiPath
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $path;

    /**
     * @var OpenApiOperation[]|array
     */
    private $operations;

    /**
     * @var OpenApiOperation
     */
    private $lastAddedOperation;

    public function __construct(?string $path=null)
    {
        $this->path = $path;

        if ($currentDef = OpenApiDefinition::getCurrent()) {
            $currentDef->addPath($this);
        }
    }

    /**
     * @param Action|array|string|callable $action
     * @param callable|null $tapOperation
     * @return $this
     */
    public function action($action, ?callable $tapOperation=null): self
    {
        if (is_array($action) || is_string($action)) {
            $action = Action::fromName($action);
        }
        
        return (
            $this->path($action->uri())
                ->addOperation(
                    OpenApiOperation::make()
                        ->fromAction($action)
                        ->tap($tapOperation ?: function () {})
                )
        );
    }

    public function get()
    {
        return $this->addOperation(OpenApiOperation::make()->method('get'));
    }

    protected function preparePath(string $path): string
    {
        if (!Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        return $path;
    }

    public function path(string $path): self
    {
        if ($definition = OpenApiDefinition::getCurrent()) {
            return $definition->findOrCreatePath($this->preparePath($path));
        }

        $this->path = $this->preparePath($path);

        return $this;
    }

    public function build(): OpenApiPathDto
    {
        return new OpenApiPathDto([
            'path' => $this->path,
            'operations' => collect($this->operations)->map->build()->all()
        ]);
    }

    public function addOperation(OpenApiOperation $operation): self
    {
        $this->operations[] = $operation;

        $this->lastAddedOperation = $operation;

        return $this;
    }

    public function getPath()
    {
        return $this->path; //todo all setters should be "withX"
    }

    public function __call($name, $arguments)
    {
        //todo: error handling
        $this->lastAddedOperation->{$name}(...$arguments);

        return $this;
    }
}