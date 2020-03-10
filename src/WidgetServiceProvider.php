<?php

namespace BYanelli\SuperFactory;

use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('foo', function () {return 'bar';});
    }
}