<?php

namespace BYanelli\OpenApiLaravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use mikehaertl\shellcommand\Command as ShellCommand;

class Serve extends Command
{
    protected $signature = 'openapi:serve {--definition=main} {--generator=html2} {--port=9000}';

    public function handle()
    {
        [$definition, $generator, $port] = [$this->option('definition'), $this->option('generator'), $this->option('port')];

        $generatedPath = $this->generateInTempPath($definition, $generator);

        $this->line("Serving \"{$generator}\" generated documentation on http://localhost:{$port}");

        (new ShellCommand("php -S localhost:{$port} -t {$generatedPath}"))->execute();
    }

    private function generateInTempPath(string $definition, string $generator): string
    {
        $path = tempnam(sys_get_temp_dir(), 'openapi-generated');

        unlink($path);
        mkdir($path);

        Artisan::call('openapi:generate', [
            '--definition' => $definition,
            'generator' => $generator,
            'output' => $path,
        ]);

        return $path;
    }
}