<?php

namespace BYanelli\OpenApiLaravel\Console;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use Illuminate\Console\Command;

class Generate extends Command
{
    protected $signature = 'openapi:generate {--definition=main}';

    public function handle()
    {
        $definitionName = $this->option('definition');

        $definitionPath = base_path("openapi/{$definitionName}/definition.php");

        $definition = OpenApiDefinitionBuilder::with(function () use ($definitionPath) {
            require $definitionPath;
        });

        echo (json_encode($definition->build()->toArray(), JSON_PRETTY_PRINT));
    }
}