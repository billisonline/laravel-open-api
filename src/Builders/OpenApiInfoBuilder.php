<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiInfo;
use Illuminate\Support\Traits\Tappable;

class OpenApiInfoBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $version;

    public function __construct()
    {
        if ($currentDef = OpenApiDefinitionBuilder::getCurrent()) {
            $currentDef->info($this);
        }
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function version(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function build(): OpenApiInfo
    {
        return new OpenApiInfo(['title' => $this->title, 'version' => $this->version]);
    }
}