<?php

use BYanelli\OpenApiLaravel\Builders\KeyedResponseSchemaWrapper;
use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiOperationBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiResponseBuilder;
use TestApp\Http\Controllers\PostController;
use TestApp\Http\Resources\Post;

OpenApiDefinitionBuilder::getCurrent()
    ->responseSchemaWrapper(new KeyedResponseSchemaWrapper('data'));

OpenApiInfoBuilder::make()->title('test')->version('1.0');

OpenApiPathBuilder::make()->fromActionName([PostController::class, 'index']);
OpenApiPathBuilder::make()->fromActionName([PostController::class, 'show'], function (OpenApiOperationBuilder $operation) {
    $operation->addResponse(OpenApiResponseBuilder::make()->fromResource(Post::class));
});
OpenApiPathBuilder::make()->fromActionName([PostController::class, 'store']);