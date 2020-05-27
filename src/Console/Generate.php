<?php

namespace BYanelli\OpenApiLaravel\Console;

use Illuminate\Support\Facades\Artisan;
use mikehaertl\shellcommand\Command;

class Generate extends \Illuminate\Console\Command
{
    protected $signature = 'openapi:generate {--definition=main} {generator} {output}';

    public function handle()
    {
        $specPath = tempnam(sys_get_temp_dir(), 'openapi-spec');

        Artisan::call('openapi:spec', [
            '--definition' => $this->option('definition'),
            '--output' => $specPath
        ]);

        $generator = new Command('openapi-generator generate');

        $generator
            ->addArg('--generator-name=', $this->argument('generator'))
            ->addArg('--input-spec=', $specPath)
            ->addArg('--output=', $this->argument('output'));

        $result = $generator->execute();

        unlink($specPath);

        $this->info($generator->getOutput());

        $this->error($generator->getError());

        return $result? 0 : 1;
    }
}