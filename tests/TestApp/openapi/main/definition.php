<?php

use BYanelli\OpenApiLaravel\Objects\KeyedResponseSchemaWrapper;
use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\OpenApiGroup;
use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use BYanelli\OpenApiLaravel\Objects\OpenApiPath;
use BYanelli\OpenApiLaravel\Objects\OpenApiResponse;
use TestApp\Http\Controllers\PostController;
use TestApp\Http\Resources\Post;

OpenApiDefinition::current()
    ->responseSchemaWrapper(new KeyedResponseSchemaWrapper('data'));

OpenApiInfo::make()->title('test')->version('1.0');

OpenApiPath::make()->action([PostController::class, 'index']);
OpenApiPath::make()->action([PostController::class, 'show'], function (OpenApiOperation $operation) {
    $operation->response(OpenApiResponse::make()->fromResource(Post::class));
});

OpenApiGroup::make()->usingBearerTokenAuth()->operations(function () {
    OpenApiPath::make()->action([PostController::class, 'store'])
        ->request([
            'title' => 'string',
            'body' => 'string',
        ]);
});