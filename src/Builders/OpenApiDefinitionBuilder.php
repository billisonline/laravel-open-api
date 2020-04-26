<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinitionBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var self|null
     */
    protected static $current = null;

    /**
     * @var OpenApiPathBuilder[]|array
     */
    private $paths = [];

    /**
     * @var OpenApiInfoBuilder
     */
    private $info;

    public static function with(callable $callback): self
    {
        $current = static::$current = new static;

        $callback();

        static::$current = null;

        return $current;
    }

    public static function getCurrent(): ?self
    {
        return static::$current;
    }

    public function addPath(OpenApiPathBuilder $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function findPath(string $pathToFind): ?OpenApiPathBuilder
    {
        return (
            collect($this->paths)
                ->filter(function (OpenApiPathBuilder $path) use ($pathToFind) {
                    return $path->getPath() == $pathToFind;
                })
                ->first()
        );
    }

    public function forgetPath(OpenApiPathBuilder $pathToForget): void
    {
        $this->paths = (
            collect($this->paths)
                ->filter(function (OpenApiPathBuilder $path) use ($pathToForget) {
                    return $path->getPath() !== $pathToForget->getPath();
                })
                ->all()
        );
    }

    public function info(OpenApiInfoBuilder $info)
    {
        $this->info = $info;

        return $this;
    }

    public function build()
    {
        return new OpenApiDefinition([
            'paths' => (
                collect($this->paths)
                    ->map(function (OpenApiPathBuilder $path) {
                        return $path->build();
                    })
                    ->all()
            ),
            'info' => $this->info->build()
        ]);
    }
}