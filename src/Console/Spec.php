<?php

namespace BYanelli\OpenApiLaravel\Console;

use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use Illuminate\Console\Command;

class Spec extends Command
{
    protected $signature = 'openapi:spec {--definition=main} {--output=}';

    public function handle()
    {
        //todo: test???

        $definitionName = $this->option('definition');

        $definitionPath = base_path("openapi/{$definitionName}/definition.php");

        $definition = OpenApiDefinition::with(function () use ($definitionPath) {
            require $definitionPath;
        });

        $content = json_encode($definition->build()->toArray(), JSON_PRETTY_PRINT);

        if ($output = $this->option('output')) {
            file_put_contents($output, $content);
        } else {
            echo $content;
        }
    }
}