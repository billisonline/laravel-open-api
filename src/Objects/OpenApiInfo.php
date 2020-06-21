<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiInfoDto;
use Illuminate\Support\Traits\Tappable;

class OpenApiInfo
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
        if ($currentDef = OpenApiDefinition::current()) {
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

    public function build(): OpenApiInfoDto
    {
        return new OpenApiInfoDto(['title' => $this->title, 'version' => $this->version]);
    }
}