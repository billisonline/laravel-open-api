<?php

namespace BYanelli\OpenApiLaravel\Tests;

use BYanelli\OpenApiLaravel\OpenApiServiceProvider;
use Illuminate\Routing\Router;
use TestApp\Providers\RouteServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    const IDENTICAL_RESULT = 1;

    protected function resolveApplicationCore($app)
    {
        $app->setBasePath(__DIR__.'/TestApp');

        parent::resolveApplicationCore($app);
    }

    protected function getPackageProviders($app)
    {
        return [
            OpenApiServiceProvider::class,
            RouteServiceProvider::class,
        ];
    }
}