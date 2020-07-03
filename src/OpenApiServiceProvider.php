<?php

namespace BYanelli\OpenApiLaravel;

use BYanelli\OpenApiLaravel\Console\Generate;
use BYanelli\OpenApiLaravel\Console\Serve;
use BYanelli\OpenApiLaravel\Console\Spec;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class OpenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        Router::macro('getRouteByAction', function ($a) {
            return $this->routes->getByAction($a);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                Spec::class,
                Generate::class,
                Serve::class,
            ]);
        }
    }
}