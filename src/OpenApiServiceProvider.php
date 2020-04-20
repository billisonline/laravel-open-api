<?php

namespace BYanelli\OpenApiLaravel;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class OpenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('foo', function () {return 'bar';});

        Router::macro('getRouteByAction', function ($a) {
            return $this->routes->getByAction($a);
        });
    }
}