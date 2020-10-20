<?php

namespace BYanelli\OpenApiLaravel\Console;

use Illuminate\Support\Facades\Artisan;
use mikehaertl\shellcommand\Command;

class Generate extends \Illuminate\Console\Command
{
    const GENERATOR_REDOC = 'redoc';

    protected $signature = 'openapi:generate {--definition=main} {generator} {output}';

    private function usingOpenApiGenerator(): bool
    {
        return !$this->usingRedoc();
    }

    private function usingRedoc(): bool
    {
        return ($this->argument('generator') === self::GENERATOR_REDOC);
    }

    public function handle()
    {
        if ($this->usingOpenApiGenerator() && !$this->openApiGeneratorCommandExists()) {
            $this->error('Command "openapi-generator" not found. Please install the OpenAPI Generator');
            $this->line('https://openapi-generator.tech/docs/installation/');
            return 1;
        }

        if ($this->usingRedoc() && !$this->redocCommandExists()) {
            $this->error('Command "redoc-cli" not found. Please install Redoc');
            $this->line('https://www.npmjs.com/package/redoc-cli/');
            return 1;
        }

        [$generator, $outputPath, $specPath] = [
            $this->argument('generator'),
            $this->argument('output'),
            $this->buildSpecInTempPath($this->option('definition')),
        ];

        if ($this->usingOpenApiGenerator()) {
            [$success, $output, $error] = $this->generateWithOpenApiGenerator($generator, $specPath, $outputPath);
        } elseif ($this->usingRedoc()) {
            [$success, $output, $error] = $this->generateWithRedoc($specPath, $outputPath);
        } else {
            throw new \Exception;
        }

        unlink($specPath);

        $this->info($output);
        $this->error($error);

        return $success? 0 : 1;
    }

    private function buildSpecInTempPath(string $definition): string
    {
        $specPath = tempnam(sys_get_temp_dir(), 'openapi-spec');

        Artisan::call('openapi:spec', [
            '--definition' => $definition,
            '--output' => $specPath
        ]);

        return $specPath;
    }

    private function jarPathOverride(): string
    {
        return env('OPENAPI_GENERATOR_JAR_PATH', '');
    }

    private function hasJarPathOverride(): bool
    {
        return !empty($this->jarPathOverride());
    }

    public function generateWithOpenApiGenerator(string $generator, string $specPath, string $output): array
    {
        if ($this->hasJarPathOverride()) {
            $javaHome = $this->getJavaHome();
            $generatorCommand = new Command("java -jar {$this->jarPathOverride()} generate");

            $generatorCommand->procEnv['JAVA_HOME'] = $javaHome;
            $generatorCommand->procEnv['PATH'] = env('PATH').':'.$javaHome;
        } else {
            $generatorCommand = new Command('openapi-generator generate');
        }

        $generatorCommand
            ->addArg('--generator-name=', $generator)
            ->addArg('--input-spec=', $specPath)
            ->addArg('--output=', $output);

        return [
            $generatorCommand->execute(),
            $generatorCommand->getOutput(),
            $generatorCommand->getError(),
        ];
    }

    private function openApiGeneratorCommandExists(): bool
    {
        if ($this->hasJarPathOverride()) {
            return file_exists($this->jarPathOverride());
        }

        return (new Command('which openapi-generator'))->execute();
    }

    public function getJavaHome(): string
    {
        $command = new Command('/usr/libexec/java_home');

        $command->execute();

        return $command->getOutput();
    }

    private function generateWithRedoc(string $specPath, string $output): array
    {
        if (is_dir($output)) {$output .= '/index.html';}

        $redocCommand = new Command('redoc-cli bundle '.$specPath);

        $redocCommand->addArg('--output=', $output);

        return [
            $redocCommand->execute(),
            $redocCommand->getOutput(),
            $redocCommand->getError(),
        ];
    }

    private function redocCommandExists(): bool
    {
        return (new Command('which redoc-cli'))->execute();
    }
}
