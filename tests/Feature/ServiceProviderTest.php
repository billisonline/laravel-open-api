<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Tests\TestCase;
use BYanelli\OpenApiLaravel\OpenApiServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function testServiceProviderWasRun()
    {
        $this->assertEquals($this->app->make('foo'), 'bar');
    }
}