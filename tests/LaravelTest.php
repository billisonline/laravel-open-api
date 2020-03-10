<?php

namespace BYanelli\SuperFactory\Tests;

use BYanelli\SuperFactory\WidgetServiceProvider;
use Orchestra\Testbench\TestCase;

class LaravelTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            WidgetServiceProvider::class,
        ];
    }

    public function testServiceProviderWasRun()
    {
        $this->assertEquals($this->app->make('foo'), 'bar');
    }
}