<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Tests\TestCase;
use Illuminate\Routing\Router;

class ServiceProviderTest extends TestCase
{
    public function testServiceProviderWasRun()
    {
        $this->assertTrue(Router::hasMacro('getRouteByAction'));
    }
}