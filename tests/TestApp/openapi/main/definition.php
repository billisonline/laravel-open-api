<?php

use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use TestApp\Http\Controllers\PostController;

OpenApiInfoBuilder::make()->title('test')->version('1.0');

OpenApiPathBuilder::make()->fromActionName([PostController::class, 'index']);
OpenApiPathBuilder::make()->fromActionName([PostController::class, 'store']);