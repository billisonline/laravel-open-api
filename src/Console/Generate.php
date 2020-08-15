<?php

namespace BYanelli\OpenApiLaravel\Console;

use Illuminate\Support\Facades\Artisan;
use mikehaertl\shellcommand\Command;

class Generate extends \Illuminate\Console\Command
{
    protected $signature = 'openapi:generate {--definition=main} {generator} {output}';

    public function handle()
    {
        if (!$this->openApiGeneratorCommandExists()) {
            $this->error('Command "openapi-generator" not found. Please install the OpenAPI Generator');
            $this->line('https://openapi-generator.tech/docs/installation/');
            return 1;
        }

        $specPath = $this->buildSpecInTempPath($this->option('definition'));

        [$success, $output, $error] = $this->generate($this->argument('generator'), $specPath, $this->argument('output'));

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

    public function generate(string $generator, string $specPath, string $output): array
    {
        if ($jarPath = env('OPENAPI_GENERATOR_JAR_PATH')) {
            $javaHome = $this->getJavaHome();
            $generatorCommand = new Command("java -jar {$jarPath}");

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

    private function openApiGeneratorCommandExists()
    {
        return (new Command('which openapi-generator'))->execute();
    }

    public function getJavaHome(): string
    {
        $command = new Command('/usr/libexec/java_home');

        $command->execute();

        return $command->getOutput();
    }
}