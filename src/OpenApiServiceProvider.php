<?php

namespace BYanelli\OpenApiLaravel;

use Illuminate\Support\ServiceProvider;

class OpenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('foo', function () {return 'bar';});
    }
}