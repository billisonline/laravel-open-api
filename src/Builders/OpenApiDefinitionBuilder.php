<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use Illuminate\Support\Traits\Tappable;

class OpenApiDefinitionBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var OpenApiPathBuilder[]|array
     */
    private $paths = [];

    /**
     * @var OpenApiInfoBuilder
     */
    private $info;

    public function addPath(OpenApiPathBuilder $path): self
    {
        $this->paths[] = $path;

        return $this;
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
                    ->map(function (OpenApiPathBuilder $path) {return $path->build();})
                    ->all()
            ),
            'info' => $this->info->build()
        ]);
    }
}