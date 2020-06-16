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

OpenApiPathBuilder::make()->action([PostController::class, 'index']);
OpenApiPathBuilder::make()->action([PostController::class, 'show'], function (OpenApiOperationBuilder $operation) {
    $operation->response(OpenApiResponseBuilder::make()->fromResource(Post::class));
});
OpenApiPathBuilder::make()->action([PostController::class, 'store'])
    ->request([
        'title' => 'string',
        'body' => 'string',
    ]);